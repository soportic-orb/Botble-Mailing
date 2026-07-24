<?php

namespace Botble\Mailing\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Mailing\Commands\MonthlyDigestCommand;
use Botble\Mailing\Commands\ProcessMailingCommand;
use Botble\Mailing\Services\MailingService;
use Botble\Mailing\Services\MailSenderFactory;
use Botble\Mailing\Services\MicrosoftOAuthService;
use Botble\Mailing\Services\UpdateService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class MailingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(MailingService::class);
        $this->app->singleton(UpdateService::class);
        $this->app->singleton(MicrosoftOAuthService::class);
        $this->app->singleton(MailSenderFactory::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/mailing')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        $this->app->register(EventServiceProvider::class);

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-mailing',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/mailing::mailing.name',
                    'icon' => 'ti ti-mail-forward',
                    'url' => route('mailing.campaigns.index'),
                    'permissions' => ['mailing.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-mailing-campaigns',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-mailing',
                    'name' => 'plugins/mailing::mailing.campaigns.name',
                    'url' => route('mailing.campaigns.index'),
                    'permissions' => ['mailing.campaigns.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-mailing-contacts',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-mailing',
                    'name' => 'plugins/mailing::mailing.contacts.name',
                    'url' => route('mailing.contacts.index'),
                    'permissions' => ['mailing.contacts.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-mailing-settings',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-mailing',
                    'name' => 'plugins/mailing::mailing.settings.menu',
                    'url' => route('mailing.settings'),
                    'permissions' => ['mailing.settings'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-mailing-update',
                    'priority' => 4,
                    'parent_id' => 'cms-plugins-mailing',
                    'name' => 'plugins/mailing::mailing.update.menu',
                    'url' => route('mailing.update'),
                    'permissions' => ['mailing.update'],
                ]);
        });

        $this->app->booted(function (): void {
            EmailHandler::addTemplateSettings(MAILING_MODULE_SCREEN_NAME, config('plugins.mailing.email', []));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessMailingCommand::class,
                MonthlyDigestCommand::class,
            ]);

            $this->app->booted(function (): void {
                $schedule = $this->app->make(Schedule::class);

                $schedule->command('mailing:process')->everyMinute()->withoutOverlapping();
                $schedule->command('mailing:digest')->hourly()->withoutOverlapping();
            });
        }
    }
}
