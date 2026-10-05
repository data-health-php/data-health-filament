<?php

declare(strict_types=1);

namespace DataHealth\Filament\Pages;

use BackedEnum;
use DataHealth\Filament\Filament\FindingTypesTable as FindingTypesTableConfiguration;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class FindingTypes extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'data-health-filament::pages.finding-types';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Finding types';

    protected static ?string $title = 'Finding types';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return config('data-health-filament.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return ((int) config('data-health-filament.navigation_sort')) + 1;
    }

    public function table(Table $table): Table
    {
        return FindingTypesTableConfiguration::configure($table);
    }
}
