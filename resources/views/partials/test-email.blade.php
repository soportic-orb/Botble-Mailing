<div class="mb-3">
    <label class="form-label" for="mailing-test-email-input">
        {{ trans('plugins/mailing::mailing.mail_driver.test_email_label') }}
    </label>
    <div class="input-group">
        <input type="email" class="form-control" id="mailing-test-email-input"
               value="{{ auth()->user()->email ?? '' }}" placeholder="email@example.com">
        <button type="button" class="btn btn-info" id="mailing-test-email-button"
                data-url="{{ route('mailing.settings.test-email') }}">
            {{ trans('plugins/mailing::mailing.mail_driver.test_email_button') }}
        </button>
    </div>
    <div class="form-hint">{{ trans('plugins/mailing::mailing.mail_driver.test_email_helper') }}</div>
</div>
<script>
    document.getElementById('mailing-test-email-button').addEventListener('click', function () {
        var button = this;
        button.disabled = true;

        fetch(button.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                email: document.getElementById('mailing-test-email-input').value,
            }),
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            button.disabled = false;

            if (data.error) {
                window.Botble ? Botble.showError(data.message) : alert(data.message);
            } else {
                window.Botble ? Botble.showSuccess(data.message) : alert(data.message);
            }
        }).catch(function (error) {
            button.disabled = false;
            alert(error);
        });
    });
</script>
