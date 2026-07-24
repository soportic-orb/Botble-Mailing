<?php

namespace Botble\Mailing\Forms;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
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
                'mailing_mail_driver',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.driver'))
                    ->helperText(trans('plugins/mailing::mailing.mail_driver.driver_helper'))
                    ->choices([
                        'default' => trans('plugins/mailing::mailing.mail_driver.drivers.default'),
                        'smtp' => trans('plugins/mailing::mailing.mail_driver.drivers.smtp'),
                        'microsoft' => trans('plugins/mailing::mailing.mail_driver.drivers.microsoft'),
                    ])
                    ->defaultValue((string) setting('mailing_mail_driver', 'default'))
            )
            ->addOpenCollapsible('mailing_mail_driver', 'smtp')
            ->add(
                'mailing_smtp_host',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_host'))
                    ->defaultValue((string) setting('mailing_smtp_host', ''))
                    ->placeholder('smtp.example.com')
            )
            ->add(
                'mailing_smtp_port',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_port'))
                    ->defaultValue((int) setting('mailing_smtp_port', 587))
                    ->addAttribute('min', 1)
                    ->addAttribute('max', 65535)
            )
            ->add(
                'mailing_smtp_username',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_username'))
                    ->defaultValue((string) setting('mailing_smtp_username', ''))
            )
            ->add(
                'mailing_smtp_password',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_password'))
                    ->defaultValue((string) setting('mailing_smtp_password', ''))
                    ->addAttribute('type', 'password')
                    ->addAttribute('autocomplete', 'new-password')
            )
            ->add(
                'mailing_smtp_encryption',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_encryption'))
                    ->choices([
                        'tls' => 'TLS / STARTTLS',
                        'ssl' => 'SSL',
                        'none' => trans('plugins/mailing::mailing.mail_driver.smtp_encryption_none'),
                    ])
                    ->defaultValue((string) setting('mailing_smtp_encryption', 'tls'))
            )
            ->add(
                'mailing_smtp_from_email',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_from_email'))
                    ->helperText(trans('plugins/mailing::mailing.mail_driver.smtp_from_email_helper'))
                    ->defaultValue((string) setting('mailing_smtp_from_email', ''))
                    ->addAttribute('type', 'email')
            )
            ->add(
                'mailing_smtp_from_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.smtp_from_name'))
                    ->defaultValue((string) setting('mailing_smtp_from_name', ''))
            )
            ->addCloseCollapsible('mailing_mail_driver', 'smtp')
            ->addOpenCollapsible('mailing_mail_driver', 'microsoft')
            ->add(
                'mailing_ms_tenant_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.ms_tenant'))
                    ->helperText(trans('plugins/mailing::mailing.mail_driver.ms_tenant_helper'))
                    ->defaultValue((string) setting('mailing_ms_tenant_id', ''))
                    ->placeholder('common')
            )
            ->add(
                'mailing_ms_client_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.ms_client_id'))
                    ->defaultValue((string) setting('mailing_ms_client_id', ''))
            )
            ->add(
                'mailing_ms_client_secret',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/mailing::mailing.mail_driver.ms_client_secret'))
                    ->helperText(trans('plugins/mailing::mailing.mail_driver.ms_client_secret_helper'))
                    ->defaultValue((string) setting('mailing_ms_client_secret', ''))
                    ->addAttribute('type', 'password')
                    ->addAttribute('autocomplete', 'new-password')
            )
            ->add(
                'mailing_ms_connect_status',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/mailing::partials.microsoft-connect')->render())
            )
            ->addCloseCollapsible('mailing_mail_driver', 'microsoft')
            ->add(
                'mailing_test_email',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/mailing::partials.test-email')->render())
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
