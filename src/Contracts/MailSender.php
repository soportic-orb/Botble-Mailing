<?php

namespace Botble\Mailing\Contracts;

interface MailSender
{
    /**
     * Deliver a single email. Must throw on delivery failure so the caller
     * can mark the recipient log as bounced.
     *
     * @throws \Throwable
     */
    public function send(string $toEmail, string $subject, string $htmlContent): void;
}
