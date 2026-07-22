<?php

namespace Botble\Mailing\Providers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Mailing\Listeners\SendPostPublishedMailing;
use Botble\Mailing\Listeners\SyncNewsletterSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreatedContentEvent::class => [
            SendPostPublishedMailing::class,
        ],
        UpdatedContentEvent::class => [
            SendPostPublishedMailing::class,
        ],
    ];

    public function boot(): void
    {
        if (class_exists('Botble\Newsletter\Events\SubscribeNewsletterEvent')) {
            $this->app['events']->listen(
                'Botble\Newsletter\Events\SubscribeNewsletterEvent',
                SyncNewsletterSubscriber::class
            );
        }
    }
}
