<?php

namespace Botble\Mailing\Services;

use Botble\Mailing\Exceptions\MailingConfigurationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * OAuth2 authorization-code flow against Microsoft Entra ID and token
 * lifecycle for sending through Microsoft Graph. Tokens are stored encrypted
 * in the settings table; Microsoft rotates refresh tokens, so the new one is
 * always persisted after each refresh.
 */
class MicrosoftOAuthService
{
    protected const SCOPE = 'offline_access https://graph.microsoft.com/Mail.Send https://graph.microsoft.com/User.Read';

    protected ?string $cachedAccessToken = null;

    public function isConfigured(): bool
    {
        return setting('mailing_ms_client_id') && setting('mailing_ms_client_secret');
    }

    public function isConnected(): bool
    {
        return (bool) setting('mailing_ms_refresh_token');
    }

    public function tenant(): string
    {
        return trim((string) setting('mailing_ms_tenant_id')) ?: 'common';
    }

    public function redirectUri(): string
    {
        return route('mailing.settings.microsoft.callback');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://login.microsoftonline.com/' . $this->tenant() . '/oauth2/v2.0/authorize?' . http_build_query([
            'client_id' => (string) setting('mailing_ms_client_id'),
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri(),
            'response_mode' => 'query',
            'scope' => self::SCOPE,
            'state' => $state,
            'prompt' => 'select_account',
        ]);
    }

    public function handleCallback(string $code): void
    {
        $data = $this->requestToken([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri(),
        ]);

        $this->storeTokens($data);

        $profile = Http::withToken($data['access_token'])
            ->acceptJson()
            ->timeout(20)
            ->get('https://graph.microsoft.com/v1.0/me');

        if ($profile->successful()) {
            setting()->set([
                'mailing_ms_account_email' => (string) ($profile->json('mail') ?: $profile->json('userPrincipalName')),
                'mailing_ms_account_name' => (string) $profile->json('displayName'),
            ]);
        }

        setting()->set('mailing_ms_connection_error', '');
        setting()->save();
    }

    /**
     * @throws MailingConfigurationException when the connection is unusable
     */
    public function getValidAccessToken(bool $force = false): string
    {
        if (! $this->isConfigured()) {
            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.ms_not_configured'));
        }

        if (! $this->isConnected()) {
            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.ms_not_connected'));
        }

        $expiresAt = (int) setting('mailing_ms_token_expires_at', 0);

        if (! $force && time() < $expiresAt) {
            if ($this->cachedAccessToken) {
                return $this->cachedAccessToken;
            }

            try {
                return $this->cachedAccessToken = Crypt::decryptString((string) setting('mailing_ms_access_token'));
            } catch (DecryptException) {
                // Fall through to a refresh (e.g. APP_KEY changed).
            }
        }

        try {
            $refreshToken = Crypt::decryptString((string) setting('mailing_ms_refresh_token'));
        } catch (DecryptException) {
            $this->disconnect();

            throw new MailingConfigurationException(trans('plugins/mailing::mailing.mail_driver.ms_reconnect_required'));
        }

        try {
            $data = $this->requestToken([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);
        } catch (MailingConfigurationException $exception) {
            setting()->set('mailing_ms_connection_error', Str::limit($exception->getMessage(), 500));
            setting()->save();

            throw $exception;
        }

        $this->storeTokens($data);

        return $data['access_token'];
    }

    public function disconnect(): void
    {
        setting()->set([
            'mailing_ms_access_token' => '',
            'mailing_ms_refresh_token' => '',
            'mailing_ms_token_expires_at' => '',
            'mailing_ms_account_email' => '',
            'mailing_ms_account_name' => '',
            'mailing_ms_connection_error' => '',
        ]);
        setting()->save();

        $this->cachedAccessToken = null;
    }

    protected function requestToken(array $params): array
    {
        $response = Http::asForm()
            ->timeout(20)
            ->post('https://login.microsoftonline.com/' . $this->tenant() . '/oauth2/v2.0/token', array_merge([
                'client_id' => (string) setting('mailing_ms_client_id'),
                'client_secret' => (string) setting('mailing_ms_client_secret'),
                'scope' => self::SCOPE,
            ], $params));

        $data = (array) $response->json();

        if (! $response->successful() || empty($data['access_token'])) {
            $error = (string) ($data['error'] ?? 'unknown_error');
            $description = (string) ($data['error_description'] ?? $response->body());

            throw new MailingConfigurationException(
                'Microsoft OAuth: ' . $error . ' — ' . Str::limit($description, 300)
            );
        }

        return $data;
    }

    protected function storeTokens(array $data): void
    {
        setting()->set([
            'mailing_ms_access_token' => Crypt::encryptString((string) $data['access_token']),
            'mailing_ms_token_expires_at' => (string) (time() + (int) ($data['expires_in'] ?? 3600) - 60),
        ]);

        if (! empty($data['refresh_token'])) {
            setting()->set('mailing_ms_refresh_token', Crypt::encryptString((string) $data['refresh_token']));
        }

        setting()->save();

        $this->cachedAccessToken = (string) $data['access_token'];
    }
}
