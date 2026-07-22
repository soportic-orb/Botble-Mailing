<?php

namespace Botble\Mailing\Listeners;

use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Mailing\Models\Contact;
use Exception;
use Illuminate\Support\Facades\Log;

class SyncNewsletterSubscriber
{
    public function handle(object $event): void
    {
        try {
            $newsletter = $event->newsletter ?? null;

            if (! $newsletter || ! $newsletter->email) {
                return;
            }

            Contact::query()->firstOrCreate(
                ['email' => $newsletter->email],
                [
                    'name' => $newsletter->name,
                    'status' => ContactStatusEnum::SUBSCRIBED,
                ]
            );
        } catch (Exception $exception) {
            Log::error('[Mailing] Unable to sync newsletter subscriber: ' . $exception->getMessage());
        }
    }
}
