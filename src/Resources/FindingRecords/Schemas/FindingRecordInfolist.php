<?php

declare(strict_types=1);

namespace DataHealth\Filament\Resources\FindingRecords\Schemas;

use DataHealth\Enums\FindingUrgency;
use DataHealth\Enums\RecordStatus;
use DataHealth\Filament\Filament\FindingActions;
use DataHealth\Filament\Support\FindingRecordAction;
use DataHealth\Models\FindingRecord;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class FindingRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Finding')->schema([
                TextEntry::make('key')->label('Type')->columnSpanFull(),
                TextEntry::make('finding_description')
                    ->label('Description')
                    ->state(fn (FindingRecord $record): ?string => FindingRecordAction::description($record))
                    ->columnSpanFull()
                    ->visible(fn (FindingRecord $record): bool => filled(FindingRecordAction::description($record))),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (RecordStatus $state): string => $state->getLabel())
                    ->color(fn (RecordStatus $state): string => match ($state) {
                        RecordStatus::Active => 'danger',
                        RecordStatus::Ignored => 'gray',
                        RecordStatus::Resolved => 'success',
                    }),
                TextEntry::make('urgency')
                    ->badge()
                    ->formatStateUsing(fn (FindingUrgency $state): string => str($state->value)->headline()->toString())
                    ->color(fn (FindingUrgency $state): string => match ($state) {
                        FindingUrgency::IMMEDIATE => 'danger',
                        FindingUrgency::SOON => 'warning',
                        FindingUrgency::NORMAL => 'info',
                        FindingUrgency::DEFERRED => 'gray',
                    }),
                TextEntry::make('worklist')->placeholder('None'),
                TextEntry::make('created_at')->label('First detected')->dateTime(),
                TextEntry::make('last_detected_at')->label('Last detected')->dateTime(),
            ])->columns(3)->columnSpanFull(),
            Section::make('Affected record')->schema([
                TextEntry::make('model_type')->label('Model'),
                TextEntry::make('model_id')->label('Model ID'),
                CodeEntry::make('context')->columnSpanFull()->copyable(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Verify')
                ->description(fn (FindingRecord $record): string => FindingRecordAction::methodDescription(
                    $record,
                    'verify',
                    FindingActions::VERIFY_DESCRIPTION,
                ))
                ->icon(Heroicon::OutlinedShieldCheck)
                ->footerActions([FindingActions::verify()])
                ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active && FindingRecordAction::canVerify($record)),
            Section::make('Resolve')
                ->description(fn (FindingRecord $record): string => FindingRecordAction::methodDescription(
                    $record,
                    'resolve',
                    FindingActions::RESOLVE_DESCRIPTION,
                ))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->footerActions([FindingActions::resolve()])
                ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active && FindingRecordAction::canResolve($record)),
            Section::make('Mark as resolved')
                ->description(FindingActions::MARK_AS_RESOLVED_DESCRIPTION)
                ->icon(Heroicon::OutlinedCheckBadge)
                ->footerActions([FindingActions::markAsResolved()])
                ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active),
            Section::make('Ignore')
                ->description(FindingActions::IGNORE_DESCRIPTION)
                ->icon(Heroicon::OutlinedEyeSlash)
                ->footerActions([FindingActions::ignore()])
                ->visible(fn (FindingRecord $record): bool => $record->status === RecordStatus::Active),
        ])->columns(2);
    }
}
