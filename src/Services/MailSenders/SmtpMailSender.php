<?php

namespace Botble\Mailing\Services\MailSenders;

use Botble\Mailing\Contracts\MailSender;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

/**
 * Sends through a custom SMTP server configured only for this plugin, using
 * an on-demand mailer (Mail::build) so the global mail configuration of the
 * installation is never touched.
 */
class SmtpMailSender implements MailSender
{
    public function send(string $toEmail, string $subject, string $htmlContent): void
    {
        $encryption = (string) setting('mailing_smtp_encryption', 'tls');

        $config = [
            'transport' => 'smtp',
            'host' => (string) setting('mailing_smtp_host'),
            'port' => (int) setting('mailing_smtp_port', 587),
            'username' => (string) setting('mailing_smtp_username') ?: null,
            'password' => (string) setting('mailing_smtp_password') ?: null,
            'timeout' => 30,
        ];

        if ($encryption === 'ssl') {
            // Implicit TLS (usually port 465). On other setups Symfony Mailer
            // negotiates STARTTLS automatically when the server offers it.
            $config['scheme'] = 'smtps';
            $config['encryption'] = 'ssl';
        }

        $fromEmail = (string) setting('mailing_smtp_from_email');
        $fromName = (string) setting('mailing_smtp_from_name');

        Mail::build($config)->html($htmlContent, function (Message $message) use ($toEmail, $subject, $fromEmail, $fromName): void {
            $message->to($toEmail)->subject($subject);

            if ($fromEmail) {
                $message->from($fromEmail, $fromName ?: null);
            }
        });
    }
}
