<?php

namespace Botble\Mailing\Forms;

use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Mailing\Http\Requests\CampaignRequest;
use Botble\Mailing\Models\Campaign;

class CampaignForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Campaign::class)
            ->setValidatorClass(CampaignRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add(
                'subject',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.campaigns.form.subject'))
                    ->placeholder(trans('plugins/mailing::mailing.campaigns.form.subject_placeholder'))
                    ->required()
                    ->maxLength(400)
            )
            ->add(
                'content',
                EditorField::class,
                EditorFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.campaigns.form.content'))
                    ->required()
            )
            ->add(
                'scheduled_at',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.campaigns.form.scheduled_at'))
                    ->placeholder('YYYY-MM-DD HH:MM')
                    ->helperText(trans('plugins/mailing::mailing.campaigns.form.scheduled_at_helper'))
            )
            ->setBreakFieldPoint('scheduled_at');
    }
}
