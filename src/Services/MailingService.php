<?php

namespace Botble\Mailing\Services;

use Botble\Base\Facades\EmailHandler;
use Botble\Mailing\Contracts\MailSender;
use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Enums\CampaignTypeEnum;
use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Mailing\Enums\LogStatusEnum;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Models\Contact;
use Botble\Mailing\Exceptions\MailingConfigurationException;
use Botble\Mailing\Models\MailingLog;
use Botble\Mailing\Services\MailSenders\DefaultMailSender;
use BadMethodCallException;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MailingService
{
    public function __construct(protected MailSenderFactory $senderFactory)
    {
    }

    /**
     * Queue the campaign for sending: create one log per subscribed contact
     * and mark the campaign as "sending" so the scheduler processes it in
     * batches (throttling rules).
     */
    public function dispatch(Campaign $campaign): void
    {
        $this->createLogs($campaign);

        $campaign->forceFill([
            'status' => CampaignStatusEnum::SENDING,
            'started_at' => Carbon::now(),
            'completed_at' => null,
        ])->save();
    }

    public function createLogs(Campaign $campaign): void
    {
        $existingContactIds = MailingLog::query()
            ->where('campaign_id', $campaign->getKey())
            ->pluck('contact_id')
            ->filter()
            ->all();

        Contact::query()
            ->where('status', ContactStatusEnum::SUBSCRIBED)
            ->when($existingContactIds, fn ($query) => $query->whereNotIn('id', $existingContactIds))
            ->chunkById(500, function (Collection $contacts) use ($campaign): void {
                $now = Carbon::now();

                $rows = $contacts->map(fn (Contact $contact) => [
                    'campaign_id' => $campaign->getKey(),
                    'contact_id' => $contact->getKey(),
                    'email' => $contact->email,
                    'status' => LogStatusEnum::PENDING,
                    'token' => Str::random(32),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                MailingLog::query()->insert($rows);
            });

        $campaign->forceFill([
            'total_recipients' => MailingLog::query()->where('campaign_id', $campaign->getKey())->count(),
        ])->save();
    }

    /**
     * Send the next batch of a "sending" campaign, honouring the anti-spam
     * throttling rules (batch size + minimum interval between batches).
     */
    public function processBatch(Campaign $campaign): void
    {
        if ($campaign->status->getValue() !== CampaignStatusEnum::SENDING) {
            return;
        }

        $batchSize = max(1, (int) setting('mailing_batch_size', 50));
        $interval = max(0, (int) setting('mailing_batch_interval', 5));

        if ($campaign->last_batch_at && $campaign->last_batch_at->addMinutes($interval)->isFuture()) {
            return;
        }

        try {
            $sender = $this->senderFactory->make();
        } catch (MailingConfigurationException $exception) {
            // Misconfigured transport: pause the batch (logs stay pending) and
            // retry on the next scheduler tick once the admin fixes the settings.
            Log::error(sprintf('[Mailing] Campaign #%d paused: %s', $campaign->getKey(), $exception->getMessage()));

            return;
        }

        $logs = MailingLog::query()
            ->where('campaign_id', $campaign->getKey())
            ->where('status', LogStatusEnum::PENDING)
            ->orderBy('id')
            ->limit($batchSize)
            ->get();

        if ($logs->isEmpty()) {
            $this->completeCampaign($campaign);

            return;
        }

        $campaign->forceFill(['last_batch_at' => Carbon::now()])->save();

        foreach ($logs as $log) {
            try {
                $this->sendToLog($campaign, $log, $sender);
            } catch (MailingConfigurationException $exception) {
                Log::error(sprintf('[Mailing] Campaign #%d batch aborted: %s', $campaign->getKey(), $exception->getMessage()));

                break;
            }
        }

        $this->refreshCounters($campaign);

        $hasPending = MailingLog::query()
            ->where('campaign_id', $campaign->getKey())
            ->where('status', LogStatusEnum::PENDING)
            ->exists();

        if (! $hasPending) {
            $this->completeCampaign($campaign);
        }
    }

    public function sendToLog(Campaign $campaign, MailingLog $log, ?MailSender $sender = null): void
    {
        $sender ??= $this->senderFactory->make();

        try {
            try {
                [$subject, $content] = $this->buildEmail($campaign, $log);

                $sender->send($log->email, $subject, $content);
            } catch (Error | BadMethodCallException $exception) {
                // If the low-level template helpers differ in this core version,
                // fall back to the official EmailHandler sending API. Only valid
                // for the default driver: EmailHandler sends through the global
                // mailer and would silently bypass a custom transport.
                if (! $sender instanceof DefaultMailSender) {
                    throw $exception;
                }

                [$templateKey, $variables] = $this->templateData($campaign, $log);

                EmailHandler::setModule(MAILING_MODULE_SCREEN_NAME)
                    ->setType('plugins')
                    ->setVariableValues($variables)
                    ->sendUsingTemplate($templateKey, $log->email);
            }

            $log->forceFill([
                'status' => LogStatusEnum::SENT,
                'sent_at' => Carbon::now(),
                'error' => null,
            ])->save();
        } catch (MailingConfigurationException $exception) {
            // Transport became unusable mid-batch (e.g. revoked OAuth consent):
            // keep the log pending and let the caller abort the batch.
            throw $exception;
        } catch (Throwable $exception) {
            $log->forceFill([
                'status' => LogStatusEnum::FAILED,
                'error' => Str::limit($exception->getMessage(), 800),
            ])->save();
        }
    }

    /**
     * Build the final subject + HTML body using the email template system of
     * Botble, so every outgoing email is wrapped with the default template
     * (header/footer) configured in Settings → Email.
     */
    protected function buildEmail(Campaign $campaign, MailingLog $log): array
    {
        [$templateKey, $variables] = $this->templateData($campaign, $log);

        EmailHandler::setModule(MAILING_MODULE_SCREEN_NAME)
            ->setType('plugins')
            ->setVariableValues($variables);

        $rawContent = get_setting_email_template_content('plugins', MAILING_MODULE_SCREEN_NAME, $templateKey);

        $content = EmailHandler::prepareData($rawContent);

        return [(string) $campaign->subject, $content];
    }

    protected function templateData(Campaign $campaign, MailingLog $log): array
    {
        $templateKey = match ($campaign->type->getValue()) {
            CampaignTypeEnum::POST_PUBLISHED => 'post-published',
            CampaignTypeEnum::MONTHLY_DIGEST => 'monthly-digest',
            default => 'campaign',
        };

        $contact = $log->contact;

        $unsubscribeUrl = $contact
            ? route('public.mailing.unsubscribe', $contact->token)
            : url('/');

        $trackingPixel = '<img src="' . route('public.mailing.track-open', $log->token)
            . '" width="1" height="1" alt="" style="display: none; max-height: 1px; max-width: 1px;" />';

        return [$templateKey, [
            'mailing_subject' => (string) $campaign->subject,
            'mailing_content' => (string) $campaign->content,
            'unsubscribe_url' => $unsubscribeUrl,
            'tracking_pixel' => $trackingPixel,
        ]];
    }

    protected function completeCampaign(Campaign $campaign): void
    {
        $this->refreshCounters($campaign);

        $campaign->forceFill([
            'status' => CampaignStatusEnum::SENT,
            'completed_at' => Carbon::now(),
        ])->save();
    }

    public function refreshCounters(Campaign $campaign): void
    {
        $counts = MailingLog::query()
            ->where('campaign_id', $campaign->getKey())
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened
            ")
            ->first();

        $campaign->forceFill([
            'total_recipients' => (int) ($counts->total ?? 0),
            'sent_count' => (int) ($counts->sent ?? 0),
            'failed_count' => (int) ($counts->failed ?? 0),
            'opened_count' => (int) ($counts->opened ?? 0),
        ])->save();
    }

    public function cancel(Campaign $campaign): void
    {
        $campaign->forceFill([
            'status' => CampaignStatusEnum::CANCELLED,
            'completed_at' => Carbon::now(),
        ])->save();
    }
}
