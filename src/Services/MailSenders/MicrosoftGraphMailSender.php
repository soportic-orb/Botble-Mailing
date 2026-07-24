<?php

namespace Botble\Mailing\Services\MailSenders;

use Botble\Mailing\Contracts\MailSender;
use Botble\Mailing\Services\MicrosoftOAuthService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Sends through the Microsoft Graph API (POST /v1.0/me/sendMail) using the
 * OAuth-connected Microsoft 365 account. Emails are sent from the
 * authenticated mailbox.
 */
class MicrosoftGraphMailSender implements MailSender
{
    public function __construct(protected MicrosoftOAuthService $oauth)
    {
    }

    public function send(string $toEmail, string $subject, string $htmlContent): void
    {
        $token = $this->oauth->getValidAccessToken();

        $response = $this->post($token, $toEmail, $subject, $htmlContent);

        if ($response->status() === 401) {
            // Token invalidated between expiry checks: force one refresh and retry once.
            $token = $this->oauth->getValidAccessToken(true);
            $response = $this->post($token, $toEmail, $subject, $htmlContent);
        }

        if ($response->status() !== 202) {
            throw new RuntimeException(
                'Microsoft Graph sendMail: HTTP ' . $response->status() . ' — ' . Str::limit((string) $response->body(), 500)
            );
        }
    }

    protected function post(string $token, string $toEmail, string $subject, string $htmlContent): Response
    {
        return Http::withToken($token)
            ->acceptJson()
            ->timeout(30)
            ->post('https://graph.microsoft.com/v1.0/me/sendMail', [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $htmlContent,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $toEmail]],
                    ],
                ],
                'saveToSentItems' => false,
            ]);
    }
}
