<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Mailing\Enums\LogStatusEnum;
use Botble\Mailing\Forms\CampaignForm;
use Botble\Mailing\Http\Requests\CampaignRequest;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Models\Contact;
use Botble\Mailing\Services\MailingService;
use Botble\Mailing\Tables\CampaignTable;

class CampaignController extends BaseController
{
    public function index(CampaignTable $table)
    {
        $this->pageTitle(trans('plugins/mailing::mailing.campaigns.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/mailing::mailing.campaigns.create'));

        return CampaignForm::create()->renderForm();
    }

    public function store(CampaignRequest $request)
    {
        $form = CampaignForm::create()->setRequest($request);
        $form->save();

        /** @var Campaign $campaign */
        $campaign = $form->getModel();

        $this->syncScheduleStatus($campaign);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('mailing.campaigns.index'))
            ->setNextUrl(route('mailing.campaigns.edit', $campaign->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Campaign $campaign)
    {
        if (! $campaign->isEditable()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('mailing.campaigns.stats', $campaign->getKey()))
                ->setMessage(trans('plugins/mailing::mailing.campaigns.not_editable'));
        }

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $campaign->name]));

        return CampaignForm::createFromModel($campaign)->renderForm();
    }

    public function update(Campaign $campaign, CampaignRequest $request)
    {
        if (! $campaign->isEditable()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('mailing.campaigns.stats', $campaign->getKey()))
                ->setMessage(trans('plugins/mailing::mailing.campaigns.not_editable'));
        }

        $form = CampaignForm::createFromModel($campaign)->setRequest($request);
        $form->save();

        $this->syncScheduleStatus($campaign->refresh());

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('mailing.campaigns.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Campaign $campaign)
    {
        return DeleteResourceAction::make($campaign);
    }

    public function stats(Campaign $campaign, MailingService $mailingService)
    {
        $this->pageTitle(trans('plugins/mailing::mailing.stats.title', ['name' => $campaign->name]));

        $mailingService->refreshCounters($campaign);
        $campaign->refresh();

        $pending = $campaign->logs()->where('status', LogStatusEnum::PENDING)->count();

        $processed = $campaign->sent_count + $campaign->failed_count;

        $stats = [
            'total' => $campaign->total_recipients,
            'processed' => $processed,
            'delivered' => $campaign->sent_count,
            'bounced' => $campaign->failed_count,
            'opened' => $campaign->opened_count,
            'pending' => $pending,
            'progress' => $campaign->total_recipients > 0
                ? round($processed / $campaign->total_recipients * 100, 1)
                : 0,
            'delivery_rate' => $processed > 0 ? round($campaign->sent_count / $processed * 100, 1) : 0,
            'bounce_rate' => $processed > 0 ? round($campaign->failed_count / $processed * 100, 1) : 0,
            'open_rate' => $campaign->sent_count > 0 ? round($campaign->opened_count / $campaign->sent_count * 100, 1) : 0,
        ];

        $logs = $campaign->logs()->orderByDesc('id')->limit(100)->get();

        return view('plugins/mailing::stats', compact('campaign', 'stats', 'logs'));
    }

    public function send(Campaign $campaign, MailingService $mailingService)
    {
        if (! $campaign->isSendable()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/mailing::mailing.campaigns.already_sending'));
        }

        $contactsCount = Contact::query()->where('status', ContactStatusEnum::SUBSCRIBED)->count();

        if (! $contactsCount) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/mailing::mailing.campaigns.no_contacts'));
        }

        $mailingService->dispatch($campaign);

        return $this
            ->httpResponse()
            ->setNextUrl(route('mailing.campaigns.stats', $campaign->getKey()))
            ->setMessage(trans('plugins/mailing::mailing.campaigns.sending_started'));
    }

    public function cancel(Campaign $campaign, MailingService $mailingService)
    {
        if (! in_array($campaign->status->getValue(), [CampaignStatusEnum::SCHEDULED, CampaignStatusEnum::SENDING])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/mailing::mailing.campaigns.cannot_cancel'));
        }

        $mailingService->cancel($campaign);

        return $this
            ->httpResponse()
            ->setNextUrl(route('mailing.campaigns.stats', $campaign->getKey()))
            ->setMessage(trans('plugins/mailing::mailing.campaigns.sending_cancelled'));
    }

    protected function syncScheduleStatus(Campaign $campaign): void
    {
        if (! $campaign->isEditable()) {
            return;
        }

        $campaign->forceFill([
            'status' => $campaign->scheduled_at
                ? CampaignStatusEnum::SCHEDULED
                : CampaignStatusEnum::DRAFT,
        ])->save();
    }
}
