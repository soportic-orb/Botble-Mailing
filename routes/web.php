<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Mailing\Http\Controllers\CampaignController;
use Botble\Mailing\Http\Controllers\ContactController;
use Botble\Mailing\Http\Controllers\MailingSettingController;
use Botble\Mailing\Http\Controllers\MicrosoftConnectController;
use Botble\Mailing\Http\Controllers\PublicTrackingController;
use Botble\Mailing\Http\Controllers\UpdateController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'mailing', 'as' => 'mailing.'], function (): void {
        Route::group(['prefix' => 'campaigns', 'as' => 'campaigns.'], function (): void {
            Route::get('{campaign}/stats', [CampaignController::class, 'stats'])->name('stats');
            Route::post('{campaign}/send', [CampaignController::class, 'send'])->name('send');
            Route::post('{campaign}/cancel', [CampaignController::class, 'cancel'])->name('cancel');

            Route::resource('', CampaignController::class)
                ->parameters(['' => 'campaign'])
                ->except(['show']);
        });

        Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function (): void {
            Route::resource('', ContactController::class)
                ->parameters(['' => 'contact'])
                ->except(['show']);
        });

        Route::get('settings', [MailingSettingController::class, 'edit'])->name('settings');
        Route::put('settings', [MailingSettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [MailingSettingController::class, 'sendTestEmail'])->name('settings.test-email');

        Route::group(['prefix' => 'settings/microsoft', 'as' => 'settings.microsoft.'], function (): void {
            Route::get('connect', [MicrosoftConnectController::class, 'redirect'])->name('connect');
            Route::get('callback', [MicrosoftConnectController::class, 'callback'])->name('callback');
            Route::post('disconnect', [MicrosoftConnectController::class, 'disconnect'])->name('disconnect');
        });

        Route::get('update', [UpdateController::class, 'index'])->name('update');
        Route::post('update', [UpdateController::class, 'run'])->name('update.run');
    });
});

Route::group(['middleware' => ['web'], 'prefix' => 'mailing', 'as' => 'public.mailing.'], function (): void {
    Route::get('track/open/{token}', [PublicTrackingController::class, 'trackOpen'])
        ->name('track-open')
        ->where('token', '[a-zA-Z0-9]+');

    Route::get('unsubscribe/{token}', [PublicTrackingController::class, 'unsubscribe'])
        ->name('unsubscribe')
        ->where('token', '[a-zA-Z0-9]+');
});
