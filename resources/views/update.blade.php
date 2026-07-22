@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('plugins/mailing::mailing.update.title') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="text-muted">{{ trans('plugins/mailing::mailing.update.current_version') }}</div>
                            <div class="h2 mb-0">v{{ $info['current'] }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted">{{ trans('plugins/mailing::mailing.update.latest_version') }}</div>
                            <div class="h2 mb-0">
                                @if ($info['latest'])
                                    v{{ $info['latest'] }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($info['error'])
                        <div class="alert alert-warning">
                            {{ trans('plugins/mailing::mailing.update.check_error') }}
                            <div class="text-muted small mt-1">{{ $info['error'] }}</div>
                        </div>
                    @elseif ($info['has_update'])
                        <div class="alert alert-info">
                            {{ trans('plugins/mailing::mailing.update.update_available', ['version' => $info['latest']]) }}
                        </div>

                        @if (! empty($info['release']['body']))
                            <div class="mb-3">
                                <strong>{{ trans('plugins/mailing::mailing.update.changelog') }}</strong>
                                <pre class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $info['release']['body'] }}</pre>
                            </div>
                        @endif

                        <form action="{{ route('mailing.update.run') }}" method="POST"
                              onsubmit="return confirm('{{ trans('plugins/mailing::mailing.update.update_confirm') }}');">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <x-core::icon name="ti ti-download" />
                                {{ trans('plugins/mailing::mailing.update.update_now') }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0">
                            {{ trans('plugins/mailing::mailing.update.up_to_date') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    {{ trans('plugins/mailing::mailing.update.source') }}:
                    <a href="https://github.com/{{ setting('mailing_github_repository', 'soportic-orb/botble-mailing') }}" target="_blank" rel="noopener">
                        {{ setting('mailing_github_repository', 'soportic-orb/botble-mailing') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
