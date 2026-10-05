<?php

declare(strict_types=1);

namespace DataHealth\Filament\Resources\FindingRecords;

use BackedEnum;
use DataHealth\Filament\Resources\FindingRecords\Pages\ListFindingRecords;
use DataHealth\Filament\Resources\FindingRecords\Pages\ViewFindingRecord;
use DataHealth\Filament\Resources\FindingRecords\Schemas\FindingRecordInfolist;
use DataHealth\Filament\Resources\FindingRecords\Tables\FindingRecordsTable;
use DataHealth\Models\FindingRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FindingRecordResource extends Resource
{
    protected static ?string $model = FindingRecord::class;

    protected static bool $shouldSkipAuthorization = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('data-health-filament.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('data-health-filament.navigation_sort');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = FindingRecord::query()->where('status', 'active')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return FindingRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FindingRecordsTable::configure($table, includeViewAction: true);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFindingRecords::route('/'),
            'view' => ViewFindingRecord::route('/{record}'),
        ];
    }
}
