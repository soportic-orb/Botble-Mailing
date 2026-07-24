<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Mailing\Services\MicrosoftOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class MicrosoftConnectController extends BaseController
{
    public function redirect(MicrosoftOAuthService $oauth)
    {
        if (! $oauth->isConfigured()) {
            return redirect()
                ->route('mailing.settings')
                ->with('error_msg', trans('plugins/mailing::mailing.mail_driver.ms_not_configured'));
        }

        $state = Str::random(40);
        session(['mailing_ms_oauth_state' => $state]);

        return redirect()->away($oauth->getAuthorizationUrl($state));
    }

    public function callback(Request $request, MicrosoftOAuthService $oauth)
    {
        if ($request->input('error')) {
            return redirect()
                ->route('mailing.settings')
                ->with('error_msg', trans('plugins/mailing::mailing.mail_driver.connect_cancelled', [
                    'error' => $request->input('error_description') ?: $request->input('error'),
                ]));
        }

        $state = (string) session()->pull('mailing_ms_oauth_state');

        if (! $state || ! hash_equals($state, (string) $request->input('state'))) {
            return redirect()
                ->route('mailing.settings')
                ->with('error_msg', trans('plugins/mailing::mailing.mail_driver.invalid_state'));
        }

        try {
            $oauth->handleCallback((string) $request->input('code'));
        } catch (Throwable $exception) {
            return redirect()
                ->route('mailing.settings')
                ->with('error_msg', Str::limit($exception->getMessage(), 300));
        }

        return redirect()
            ->route('mailing.settings')
            ->with('success_msg', trans('plugins/mailing::mailing.mail_driver.connected_success', [
                'email' => setting('mailing_ms_account_email'),
            ]));
    }

    public function disconnect(MicrosoftOAuthService $oauth)
    {
        $oauth->disconnect();

        return $this
            ->httpResponse()
            ->setNextUrl(route('mailing.settings'))
            ->setMessage(trans('plugins/mailing::mailing.mail_driver.disconnected'));
    }
}
