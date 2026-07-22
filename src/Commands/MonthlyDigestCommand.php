<?php

namespace Botble\Mailing\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Enums\CampaignTypeEnum;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Services\MailingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MonthlyDigestCommand extends Command
{
    protected $signature = 'mailing:digest {--force : Send the digest ignoring day/time checks}';

    protected $description = 'Send the monthly digest with the posts published since the last monthly mailing.';

    public function handle(MailingService $mailingService): int
    {
        if (! $this->option('force')) {
            if (! setting('mailing_monthly_enabled')) {
                return self::SUCCESS;
            }

            $now = Carbon::now();

            $day = min(max((int) setting('mailing_monthly_day', 1), 1), 31);
            // For months shorter than the configured day, send on the last day.
            $day = min($day, $now->copy()->endOfMonth()->day);

            if ($now->day !== $day) {
                return self::SUCCESS;
            }

            $time = (string) setting('mailing_monthly_time', '08:00');
            $hour = (int) Str::before($time, ':');

            if ($now->hour < $hour) {
                return self::SUCCESS;
            }

            $lastSent = setting('mailing_last_monthly_sent_at');

            if ($lastSent && Carbon::parse($lastSent)->isSameMonth($now)) {
                return self::SUCCESS;
            }
        }

        if (! class_exists('Botble\Blog\Models\Post') || ! is_plugin_active('blog')) {
            $this->warn('The Blog plugin is not active, digest skipped.');

            return self::SUCCESS;
        }

        $lastSent = setting('mailing_last_monthly_sent_at');
        $since = $lastSent ? Carbon::parse($lastSent) : Carbon::now()->subMonth();

        $posts = \Botble\Blog\Models\Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No posts published since the last monthly mailing, digest skipped.');

            return self::SUCCESS;
        }

        $content = '';

        foreach ($posts as $post) {
            $url = null;

            try {
                $url = $post->url;
            } catch (\Throwable) {
                $url = url('/');
            }

            $content .= '<div style="margin-bottom: 24px;">'
                . '<h3 style="margin: 0 0 8px;"><a href="' . e($url) . '" style="color: #206bc4; text-decoration: none;">'
                . e($post->name)
                . '</a></h3>'
                . '<p style="margin: 0; color: #555555; line-height: 1.6;">' . e($this->makeExcerpt($post)) . '</p>'
                . '</div>';
        }

        $now = Carbon::now();

        $campaign = Campaign::query()->create([
            'name' => trans('plugins/mailing::mailing.digest.campaign_name', ['date' => $now->translatedFormat('F Y')]),
            'subject' => trans('plugins/mailing::mailing.digest.subject', ['month' => $now->translatedFormat('F Y')]),
            'content' => $content,
            'type' => CampaignTypeEnum::MONTHLY_DIGEST,
            'status' => CampaignStatusEnum::DRAFT,
        ]);

        $mailingService->dispatch($campaign);

        setting()->set('mailing_last_monthly_sent_at', $now->toDateTimeString());
        setting()->save();

        $this->info(sprintf('Monthly digest queued with %d posts for %d recipients.', $posts->count(), $campaign->total_recipients));

        return self::SUCCESS;
    }

    /**
     * The first 3 lines of the post: use its description when available,
     * otherwise take the first lines of the plain-text content.
     */
    protected function makeExcerpt(object $post): string
    {
        if ($post->description) {
            return Str::limit(trim(strip_tags((string) $post->description)), 400);
        }

        $text = trim(strip_tags((string) $post->content));

        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));

        $excerpt = implode(' ', array_slice($lines, 0, 3));

        return Str::limit($excerpt, 400);
    }
}
