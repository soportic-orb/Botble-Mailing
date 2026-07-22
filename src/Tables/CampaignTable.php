<?php

namespace Botble\Mailing\Tables;

use Botble\Mailing\Models\Campaign;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CampaignTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Campaign::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('mailing.campaigns.create'))
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('mailing.campaigns.stats'),
                Column::make('subject')
                    ->title(trans('plugins/mailing::mailing.campaigns.form.subject'))
                    ->alignStart(),
                StatusColumn::make('type')
                    ->title(trans('plugins/mailing::mailing.campaigns.type')),
                StatusColumn::make(),
                Column::make('total_recipients')
                    ->title(trans('plugins/mailing::mailing.stats.total'))
                    ->width(80),
                Column::make('sent_count')
                    ->title(trans('plugins/mailing::mailing.stats.delivered'))
                    ->width(80),
                Column::make('failed_count')
                    ->title(trans('plugins/mailing::mailing.stats.bounced'))
                    ->width(80),
                Column::make('scheduled_at')
                    ->title(trans('plugins/mailing::mailing.campaigns.form.scheduled_at'))
                    ->width(120),
                CreatedAtColumn::make(),
            ])
            ->addActions([
                Action::make('stats')
                    ->route('mailing.campaigns.stats')
                    ->permission('mailing.campaigns.stats')
                    ->label(trans('plugins/mailing::mailing.campaigns.stats'))
                    ->icon('ti ti-chart-bar'),
                EditAction::make()->route('mailing.campaigns.edit'),
                DeleteAction::make()->route('mailing.campaigns.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('mailing.campaigns.destroy'),
            ])
            ->queryUsing(fn (Builder $query) => $query->select([
                'id',
                'name',
                'subject',
                'type',
                'status',
                'total_recipients',
                'sent_count',
                'failed_count',
                'scheduled_at',
                'created_at',
            ]));
    }
}
