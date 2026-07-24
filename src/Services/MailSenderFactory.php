<?php

namespace Botble\Mailing\Services;

use Botble\Mailing\Contracts\MailSender;
use Botble\Mailing\Exceptions\MailingConfigurationException;
use Botble\Mailing\Services\MailSenders\DefaultMailSender;
use Botble\Mailing\Services\MailSenders\MicrosoftGraphMailSender;
use Botble\Mailing\Services\MailSenders\SmtpMailSender;

class MailSenderFactory
{
    public function __construct(protected MicrosoftOAuthService $oauth)
    {
    }

    /**
     * @throws MailingConfigurationException when the selected driver is not usable
     */
    public function make(): MailSender
    {
        return match ((string) setting('mailing_mail_driver', 'default')) {
            'smtp' => $this->makeSmtp(),
            'microsoft' => $this->makeMicrosoft(),
            default => new DefaultMailSender(),
        };
    }

    protected function makeSmtp(): SmtpMailSender
    {
        if (! setting('mailing_smtp_host')) {
            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.smtp_not_configured'));
        }

        return new SmtpMailSender();
    }

    protected function makeMicrosoft(): MicrosoftGraphMailSender
    {
        if (! $this->oauth->isConfigured()) {
            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.ms_not_configured'));
        }

        if (! $this->oauth->isConnected()) {
            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.ms_not_connected'));
        }

        return new MicrosoftGraphMailSender($this->oauth);
    }
}
