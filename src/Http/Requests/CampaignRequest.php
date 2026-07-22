<?php

namespace Botble\Mailing\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CampaignRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:400'],
            'content' => ['required', 'string'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
