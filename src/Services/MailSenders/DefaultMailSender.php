<?php

namespace Botble\Mailing\Services\MailSenders;

use Botble\Mailing\Contracts\MailSender;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

/**
 * Sends through the mailer configured in the Botble installation (default).
 */
class DefaultMailSender implements MailSender
{
    public function send(string $toEmail, string $subject, string $htmlContent): void
    {
        Mail::html($htmlContent, function (Message $message) use ($toEmail, $subject): void {
            $message->to($toEmail)->subject($subject);
        });
    }
}
