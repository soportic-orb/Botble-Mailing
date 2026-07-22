<?php

namespace Botble\Mailing\Forms;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Mailing\Http\Requests\MailingSettingRequest;
use Botble\Setting\Forms\SettingForm;

class MailingSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/mailing::mailing.settings.title'))
            ->setSectionDescription(trans('plugins/mailing::mailing.settings.description'))
            ->setValidatorClass(MailingSettingRequest::class)
            ->setUrl(route('mailing.settings.update'))
            ->add(
                'mailing_auto_post_enabled',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.auto_post_enabled'))
                    ->helperText(trans('plugins/mailing::mailing.settings.auto_post_enabled_helper'))
                    ->defaultValue((bool) setting('mailing_auto_post_enabled', false))
            )
            ->add(
                'mailing_monthly_enabled',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.monthly_enabled'))
                    ->helperText(trans('plugins/mailing::mailing.settings.monthly_enabled_helper'))
                    ->defaultValue((bool) setting('mailing_monthly_enabled', false))
            )
            ->add(
                'mailing_monthly_day',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.monthly_day'))
                    ->helperText(trans('plugins/mailing::mailing.settings.monthly_day_helper'))
                    ->defaultValue((int) setting('mailing_monthly_day', 1))
                    ->addAttribute('min', 1)
                    ->addAttribute('max', 31)
            )
            ->add(
                'mailing_monthly_time',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.monthly_time'))
                    ->helperText(trans('plugins/mailing::mailing.settings.monthly_time_helper'))
                    ->defaultValue((string) setting('mailing_monthly_time', '08:00'))
                    ->placeholder('08:00')
            )
            ->add(
                'mailing_batch_size',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.batch_size'))
                    ->helperText(trans('plugins/mailing::mailing.settings.batch_size_helper'))
                    ->defaultValue((int) setting('mailing_batch_size', 50))
                    ->addAttribute('min', 1)
                    ->addAttribute('max', 1000)
            )
            ->add(
                'mailing_batch_interval',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.batch_interval'))
                    ->helperText(trans('plugins/mailing::mailing.settings.batch_interval_helper'))
                    ->defaultValue((int) setting('mailing_batch_interval', 5))
                    ->addAttribute('min', 0)
                    ->addAttribute('max', 1440)
            )
            ->add(
                'mailing_github_repository',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.github_repository'))
                    ->helperText(trans('plugins/mailing::mailing.settings.github_repository_helper'))
                    ->defaultValue((string) setting('mailing_github_repository', 'soportic-orb/botble-mailing'))
                    ->placeholder('owner/repository')
            )
            ->add(
                'mailing_github_token',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.settings.github_token'))
                    ->helperText(trans('plugins/mailing::mailing.settings.github_token_helper'))
                    ->defaultValue((string) setting('mailing_github_token', ''))
            );
    }
}
