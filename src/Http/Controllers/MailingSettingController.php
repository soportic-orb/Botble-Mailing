<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Mailing\Forms\MailingSettingForm;
use Botble\Mailing\Http\Requests\MailingSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class MailingSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/mailing::mailing.settings.title'));

        return MailingSettingForm::create()->renderForm();
    }

    public function update(MailingSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
