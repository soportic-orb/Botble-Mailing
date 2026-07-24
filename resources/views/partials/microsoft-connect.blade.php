@php
    /** @var \Botble\Mailing\Services\MicrosoftOAuthService $oauth */
    $oauth = app(\Botble\Mailing\Services\MicrosoftOAuthService::class);
    $configured = $oauth->isConfigured();
    $connected = $oauth->isConnected();
@endphp

<div class="mb-3">
    @if (setting('mailing_ms_connection_error'))
        <div class="alert alert-warning">
            <strong>{{ trans('plugins/mailing::mailing.mail_driver.connection_error_title') }}</strong>
            <div class="small mt-1">{{ setting('mailing_ms_connection_error') }}</div>
        </div>
    @endif

    @if (! $configured)
        <div class="alert alert-info mb-2">
            {{ trans('plugins/mailing::mailing.mail_driver.ms_save_credentials_first') }}
        </div>
    @elseif (! $connected)
        <a href="{{ route('mailing.settings.microsoft.connect') }}" class="btn btn-primary">
            {{ trans('plugins/mailing::mailing.mail_driver.connect') }}
        </a>
    @else
        <div class="alert alert-success mb-2">
            {{ trans('plugins/mailing::mailing.mail_driver.connected_as') }}
            <strong>{{ setting('mailing_ms_account_name') }}</strong>
            ({{ setting('mailing_ms_account_email') }})
        </div>
        <button type="button" class="btn btn-outline-danger" id="mailing-ms-disconnect"
                data-url="{{ route('mailing.settings.microsoft.disconnect') }}"
                data-confirm="{{ trans('plugins/mailing::mailing.mail_driver.disconnect_confirm') }}">
            {{ trans('plugins/mailing::mailing.mail_driver.disconnect') }}
        </button>
        <script>
            document.getElementById('mailing-ms-disconnect').addEventListener('click', function () {
                if (! confirm(this.dataset.confirm)) {
                    return;
                }

                var button = this;
                button.disabled = true;

                fetch(button.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                }).then(function () {
                    window.location.reload();
                }).catch(function () {
                    button.disabled = false;
                });
            });
        </script>
    @endif

    <div class="form-hint mt-2">
        {{ trans('plugins/mailing::mailing.mail_driver.redirect_uri_helper') }}
        <br>
        <code>{{ route('mailing.settings.microsoft.callback') }}</code>
    </div>
</div>
