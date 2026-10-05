<?php

declare(strict_types=1);

namespace DataHealth\Filament\Livewire;

use DataHealth\Filament\Resources\FindingRecords\Tables\FindingRecordsTable;
use DataHealth\Models\FindingRecord;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ModelFindingsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public Model $model;

    public function mount(Model $model): void
    {
        $this->model = $model;
    }

    public function table(Table $table): Table
    {
        return FindingRecordsTable::configure(
            $table->query(
                FindingRecord::query()
                    ->where('model_type', $this->model->getMorphClass())
                    ->where('model_id', $this->model->getKey()),
            ),
        );
    }

    public function render(): View
    {
        return app(Factory::class)->file(
            dirname(__DIR__, 2).'/resources/views/livewire/model-findings-table.blade.php',
        );
    }
}
