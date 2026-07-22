<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ trans('plugins/mailing::mailing.unsubscribe.title') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .box {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            padding: 40px;
            max-width: 480px;
            text-align: center;
        }
        h1 { font-size: 20px; margin: 0 0 12px; color: #1f2937; }
        p { color: #6b7280; margin: 0; line-height: 1.6; }
        a { color: #206bc4; }
    </style>
</head>
<body>
    <div class="box">
        <h1>{{ trans('plugins/mailing::mailing.unsubscribe.title') }}</h1>
        <p>
            @if ($success)
                {{ trans('plugins/mailing::mailing.unsubscribe.success') }}
            @else
                {{ trans('plugins/mailing::mailing.unsubscribe.invalid') }}
            @endif
        </p>
        <p style="margin-top: 16px;">
            <a href="{{ url('/') }}">{{ trans('plugins/mailing::mailing.unsubscribe.back_home') }}</a>
        </p>
    </div>
</body>
</html>
