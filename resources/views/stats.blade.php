@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        $statusValue = $campaign->status->getValue();
        $draft = \Botble\Mailing\Enums\CampaignStatusEnum::DRAFT;
        $scheduled = \Botble\Mailing\Enums\CampaignStatusEnum::SCHEDULED;
        $sending = \Botble\Mailing\Enums\CampaignStatusEnum::SENDING;
        $cancelled = \Botble\Mailing\Enums\CampaignStatusEnum::CANCELLED;
    @endphp

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="mb-1">{{ $campaign->name }}</h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {!! $campaign->type->toHtml() !!}
                    {!! $campaign->status->toHtml() !!}
                    @if ($campaign->scheduled_at)
                        <span class="text-muted">
                            {{ trans('plugins/mailing::mailing.stats.scheduled_for', ['date' => $campaign->scheduled_at]) }}
                        </span>
                    @endif
                    @if ($campaign->completed_at)
                        <span class="text-muted">
                            {{ trans('plugins/mailing::mailing.stats.completed_at', ['date' => $campaign->completed_at]) }}
                        </span>
                    @endif
                </div>
                <div class="text-muted mt-1">{{ $campaign->subject }}</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if (in_array($statusValue, [$draft, $scheduled, $cancelled]))
                    <form action="{{ route('mailing.campaigns.send', $campaign->getKey()) }}" method="POST"
                          onsubmit="return confirm('{{ trans('plugins/mailing::mailing.campaigns.send_confirm') }}');">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <x-core::icon name="ti ti-send" />
                            {{ trans('plugins/mailing::mailing.campaigns.send_now') }}
                        </button>
                    </form>
                @endif
                @if (in_array($statusValue, [$scheduled, $sending]))
                    <form action="{{ route('mailing.campaigns.cancel', $campaign->getKey()) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            {{ trans('plugins/mailing::mailing.campaigns.cancel_sending') }}
                        </button>
                    </form>
                @endif
                @if ($campaign->isEditable())
                    <a href="{{ route('mailing.campaigns.edit', $campaign->getKey()) }}" class="btn btn-secondary">
                        {{ trans('core/base::forms.edit') }}
                    </a>
                @endif
                <a href="{{ route('mailing.campaigns.index') }}" class="btn btn-outline-secondary">
                    {{ trans('plugins/mailing::mailing.stats.back_to_list') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-3">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0">{{ number_format($stats['total']) }}</div>
                    <div class="text-muted">{{ trans('plugins/mailing::mailing.stats.total') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0">{{ number_format($stats['processed']) }}</div>
                    <div class="text-muted">{{ trans('plugins/mailing::mailing.stats.processed') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0 text-success">{{ number_format($stats['delivered']) }}</div>
                    <div class="text-muted">
                        {{ trans('plugins/mailing::mailing.stats.delivered') }}
                        ({{ $stats['delivery_rate'] }}%)
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0 text-danger">{{ number_format($stats['bounced']) }}</div>
                    <div class="text-muted">
                        {{ trans('plugins/mailing::mailing.stats.bounced') }}
                        ({{ $stats['bounce_rate'] }}%)
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0 text-info">{{ number_format($stats['opened']) }}</div>
                    <div class="text-muted">
                        {{ trans('plugins/mailing::mailing.stats.opened') }}
                        ({{ $stats['open_rate'] }}%)
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 mb-0">{{ number_format($stats['pending']) }}</div>
                    <div class="text-muted">{{ trans('plugins/mailing::mailing.stats.pending') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-1">
                <strong>{{ trans('plugins/mailing::mailing.stats.progress') }}</strong>
                <span>{{ $stats['progress'] }}%</span>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['progress'] }}%"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ trans('plugins/mailing::mailing.stats.recent_logs') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ trans('plugins/mailing::mailing.stats.log_email') }}</th>
                        <th>{{ trans('plugins/mailing::mailing.stats.log_status') }}</th>
                        <th>{{ trans('plugins/mailing::mailing.stats.log_sent_at') }}</th>
                        <th>{{ trans('plugins/mailing::mailing.stats.log_opened_at') }}</th>
                        <th>{{ trans('plugins/mailing::mailing.stats.log_error') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->email }}</td>
                            <td>{!! $log->status->toHtml() !!}</td>
                            <td>{{ $log->sent_at ?: '—' }}</td>
                            <td>{{ $log->opened_at ?: '—' }}</td>
                            <td class="text-muted">{{ $log->error ? \Illuminate\Support\Str::limit($log->error, 120) : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                {{ trans('plugins/mailing::mailing.stats.no_logs') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
