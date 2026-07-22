<?php

namespace Botble\Mailing\Tables;

use Botble\Mailing\Models\Contact;
use Botble\Table\Abstracts\TableAbstract;
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

class ContactTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Contact::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('mailing.contacts.create'))
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('mailing.contacts.edit'),
                Column::make('email')
                    ->title(trans('core/base::forms.email'))
                    ->alignStart(),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addActions([
                EditAction::make()->route('mailing.contacts.edit'),
                DeleteAction::make()->route('mailing.contacts.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('mailing.contacts.destroy'),
            ])
            ->queryUsing(fn (Builder $query) => $query->select([
                'id',
                'name',
                'email',
                'status',
                'created_at',
            ]));
    }
}
