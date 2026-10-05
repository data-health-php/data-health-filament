<?php

declare(strict_types=1);

namespace DataHealth\Filament\Livewire;

use DataHealth\Filament\Filament\FindingTypesTable as FindingTypesTableConfiguration;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FindingTypesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return FindingTypesTableConfiguration::configure($table);
    }

    public function render(): View
    {
        return app(Factory::class)->file(
            dirname(__DIR__, 2).'/resources/views/livewire/finding-types-table.blade.php',
        );
    }
}
