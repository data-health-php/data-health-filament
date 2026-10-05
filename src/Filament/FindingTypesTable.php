<?php

declare(strict_types=1);

namespace DataHealth\Filament\Filament;

use DataHealth\DataHealthManager;
use DataHealth\Filament\Support\FindingDetection;
use DataHealth\Filament\Support\FindingType;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

final class FindingTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->records(function (?string $search): Collection {
                $records = app(DataHealthManager::class)->registry()->all()
                    ->map(fn (string $class): array => FindingType::fromClass($class)->toArray())
                    ->values();

                if (blank($search)) {
                    return $records;
                }

                return $records->filter(fn (array $record): bool => str($record['key'].' '.$record['class'].' '.$record['description'])
                    ->contains($search, ignoreCase: true));
            })
            ->paginated(false)
            ->columns([
                TextColumn::make('key')->label('Finding')->searchable(),
                TextColumn::make('description')->wrap()->placeholder('No description'),
                TextColumn::make('worklist')->badge()->placeholder('None'),
                TextColumn::make('urgency')->badge()->placeholder('Normal'),
                IconColumn::make('canVerify')->label('Verify')->boolean(),
                IconColumn::make('canResolve')->label('Resolve')->boolean(),
                IconColumn::make('isScheduled')->label('Scheduled')->boolean(),
            ])
            ->recordActions([
                Action::make('detect')
                    ->icon(Heroicon::OutlinedPlay)
                    ->requiresConfirmation()
                    ->visible(fn (array $record): bool => $record['canDetect'])
                    ->modalDescription(fn (array $record): ?string => $record['class']::getMethodDescription('detect'))
                    ->action(function (array $record): void {
                        $result = FindingDetection::run($record['class']);

                        Notification::make()
                            ->success()
                            ->title('Detection complete')
                            ->body(is_int($result) ? trans_choice(':count finding detected|:count findings detected', $result, ['count' => $result]) : null)
                            ->send();
                    }),
            ]);
    }
}
