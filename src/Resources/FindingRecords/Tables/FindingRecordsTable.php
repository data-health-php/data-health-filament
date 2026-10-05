<?php

declare(strict_types=1);

namespace DataHealth\Filament\Resources\FindingRecords\Tables;

use DataHealth\Enums\FindingUrgency;
use DataHealth\Enums\RecordStatus;
use DataHealth\Filament\Filament\FindingActions;
use DataHealth\Filament\Support\FindingRecordAction;
use DataHealth\Filament\Support\ModelViewUrl;
use DataHealth\Models\FindingRecord;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class FindingRecordsTable
{
    public static function configure(Table $table, bool $includeViewAction = false): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => ModelViewUrl::shouldEagerLoadModel()
                ? $query->with('model')
                : $query)
            ->defaultSort('last_detected_at', 'desc')
            ->columns([
                TextColumn::make('key')->label('Finding')->searchable()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (RecordStatus $state): string => $state->getLabel())
                    ->color(fn (RecordStatus $state): string => match ($state) {
                        RecordStatus::Active => 'danger',
                        RecordStatus::Ignored => 'gray',
                        RecordStatus::Resolved => 'success',
                    })
                    ->sortable(),
                TextColumn::make('urgency')
                    ->badge()
                    ->formatStateUsing(fn (FindingUrgency $state): string => str($state->value)->headline()->toString())
                    ->color(fn (FindingUrgency $state): string => match ($state) {
                        FindingUrgency::IMMEDIATE => 'danger',
                        FindingUrgency::SOON => 'warning',
                        FindingUrgency::NORMAL => 'info',
                        FindingUrgency::DEFERRED => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('model_type')->label('Model')
                    ->formatStateUsing(fn (FindingRecord $record): string => class_basename($record->model_type).'#'.$record->model_id)
                    ->url(fn (FindingRecord $record): ?string => ModelViewUrl::resolve($record))
                    ->searchable(),
                TextColumn::make('worklist')->badge()->placeholder('None')->searchable()->toggleable(),
                TextColumn::make('last_detected_at')->label('Last detected')->since()->dateTimeTooltip()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->default(RecordStatus::Active->value)
                    ->options(collect(RecordStatus::cases())->mapWithKeys(
                        fn (RecordStatus $status): array => [$status->value => $status->getLabel()],
                    )),
                SelectFilter::make('urgency')->options(collect(FindingUrgency::cases())->mapWithKeys(
                    fn (FindingUrgency $urgency): array => [$urgency->value => str($urgency->value)->headline()->toString()],
                )),
                SelectFilter::make('key')->label('Finding')->options(
                    fn (): array => FindingRecord::query()->distinct()->orderBy('key')->pluck('key', 'key')->all(),
                )->searchable(),
                SelectFilter::make('worklist')->options(
                    fn (): array => FindingRecord::query()->whereNotNull('worklist')->distinct()->orderBy('worklist')->pluck('worklist', 'worklist')->all(),
                )->searchable(),
            ])
            ->recordActions([
                ...($includeViewAction ? [ViewAction::make()] : []),
                ...FindingActions::all(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('ignore')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                if ($record instanceof FindingRecord) {
                                    FindingRecordAction::ignore($record);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('reopen')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                if ($record instanceof FindingRecord) {
                                    FindingRecordAction::reopen($record);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
