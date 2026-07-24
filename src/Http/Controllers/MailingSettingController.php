<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Mailing\Forms\MailingSettingForm;
use Botble\Mailing\Http\Requests\MailingSettingRequest;
use Botble\Mailing\Services\MailSenderFactory;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class MailingSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/mailing::mailing.settings.title'));

        return MailingSettingForm::create()->renderForm();
    }

    public function update(MailingSettingRequest $request)
    {
        // Token/state settings are managed exclusively by the OAuth service.
        $data = collect($request->validated())
            ->except([
                'mailing_ms_access_token',
                'mailing_ms_refresh_token',
                'mailing_ms_token_expires_at',
                'mailing_ms_account_email',
                'mailing_ms_account_name',
                'mailing_ms_connection_error',
            ])
            ->all();

        return $this->performUpdate($data);
    }

    public function sendTestEmail(Request $request, MailSenderFactory $senderFactory)
    {
        $request->validate(['email' => ['nullable', 'email']]);

        $email = $request->input('email') ?: $request->user()->email;

        try {
            $senderFactory->make()->send(
                $email,
                trans('plugins/mailing::mailing.mail_driver.test_subject'),
                '<p>' . trans('plugins/mailing::mailing.mail_driver.test_body') . '</p>'
            );

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/mailing::mailing.mail_driver.test_sent', ['email' => $email]));
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(Str::limit($exception->getMessage(), 300));
        }
    }
}
