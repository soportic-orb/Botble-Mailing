<?php

namespace Botble\Mailing\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Enums\CampaignTypeEnum;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Services\MailingService;
use Exception;
use Illuminate\Support\Facades\Log;

class SendPostPublishedMailing
{
    public function __construct(protected MailingService $mailingService)
    {
    }

    public function handle(CreatedContentEvent|UpdatedContentEvent $event): void
    {
        try {
            if (! setting('mailing_auto_post_enabled')) {
                return;
            }

            if (! class_exists('Botble\Blog\Models\Post') || ! is_plugin_active('blog')) {
                return;
            }

            $post = $event->data;

            if (! $post instanceof \Botble\Blog\Models\Post) {
                return;
            }

            if ($post->status->getValue() !== BaseStatusEnum::PUBLISHED) {
                return;
            }

            $exists = Campaign::query()
                ->where('type', CampaignTypeEnum::POST_PUBLISHED)
                ->where('post_id', $post->getKey())
                ->exists();

            if ($exists) {
                return;
            }

            $campaign = Campaign::query()->create([
                'name' => trans('plugins/mailing::mailing.auto_post.campaign_name', ['title' => $post->name]),
                'subject' => $post->name,
                'content' => (string) $post->content,
                'type' => CampaignTypeEnum::POST_PUBLISHED,
                'status' => CampaignStatusEnum::DRAFT,
                'post_id' => $post->getKey(),
            ]);

            $this->mailingService->dispatch($campaign);
        } catch (Exception $exception) {
            Log::error('[Mailing] Unable to create automatic campaign for published post: ' . $exception->getMessage());
        }
    }
}
