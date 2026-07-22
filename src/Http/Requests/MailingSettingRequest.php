<?php

namespace Botble\Mailing\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MailingSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'mailing_auto_post_enabled' => ['nullable', 'in:0,1'],
            'mailing_monthly_enabled' => ['nullable', 'in:0,1'],
            'mailing_monthly_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'mailing_monthly_time' => ['nullable', 'date_format:H:i'],
            'mailing_batch_size' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'mailing_batch_interval' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'mailing_github_repository' => ['nullable', 'string', 'max:255', 'regex:/^[\w.-]+\/[\w.-]+$/'],
            'mailing_github_token' => ['nullable', 'string', 'max:255'],
        ];
    }
}
