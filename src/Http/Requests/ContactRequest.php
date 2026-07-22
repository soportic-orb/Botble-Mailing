<?php

namespace Botble\Mailing\Http\Requests;

use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ContactRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('mailing_contacts', 'email')->ignore($this->route('contact')),
            ],
            'status' => ['required', Rule::in(ContactStatusEnum::values())],
        ];
    }
}
