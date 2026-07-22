<?php

namespace Botble\Mailing\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Mailing\Http\Requests\ContactRequest;
use Botble\Mailing\Models\Contact;

class ContactForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Contact::class)
            ->setValidatorClass(ContactRequest::class)
            ->add('name', TextField::class, NameFieldOption::make())
            ->add(
                'email',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/base::forms.email'))
                    ->required()
                    ->addAttribute('type', 'email')
                    ->maxLength(255)
            )
            ->add(
                'status',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/base::tables.status'))
                    ->choices(ContactStatusEnum::labels())
                    ->defaultValue(ContactStatusEnum::SUBSCRIBED)
            )
            ->setBreakFieldPoint('status');
    }
}
