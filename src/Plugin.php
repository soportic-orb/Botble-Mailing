<?php

namespace Botble\Mailing;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('mailing_logs');
        Schema::dropIfExists('mailing_campaigns');
        Schema::dropIfExists('mailing_contacts');

        Setting::delete([
            'mailing_auto_post_enabled',
            'mailing_monthly_enabled',
            'mailing_monthly_day',
            'mailing_monthly_time',
            'mailing_batch_size',
            'mailing_batch_interval',
            'mailing_github_repository',
            'mailing_github_token',
            'mailing_last_monthly_sent_at',
        ]);
    }
}
