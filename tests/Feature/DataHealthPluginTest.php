<?php

declare(strict_types=1);

use DataHealth\DataHealthManager;
use DataHealth\Enums\FindingUrgency;
use DataHealth\Enums\RecordStatus;
use DataHealth\Facades\DataHealth;
use DataHealth\Filament\DataHealthPlugin;
use DataHealth\Filament\Livewire\FindingRecordsTable as StandaloneFindingRecordsTable;
use DataHealth\Filament\Livewire\FindingTypesTable as StandaloneFindingTypesTable;
use DataHealth\Filament\Livewire\ModelFindingsTable;
use DataHealth\Filament\Pages\FindingTypes;
use DataHealth\Filament\Resources\FindingRecords\FindingRecordResource;
use DataHealth\Filament\Tests\Fixtures\ActionableFinding;
use DataHealth\Filament\Tests\Fixtures\Models\TestModel;
use DataHealth\Filament\Tests\Fixtures\TestAssigneeTypes;
use DataHealth\Filament\Widgets\FindingStatsOverview;
use DataHealth\Finding;
use DataHealth\Models\FindingRecord;
use Filament\Panel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

it('registers the core data health components with a panel', function () {
    $panel = Panel::make()->id('test');

    DataHealthPlugin::make()->register($panel);

    expect($panel->getResources())->toContain(FindingRecordResource::class)
        ->and($panel->getPages())->toContain(FindingTypes::class)
        ->and($panel->getWidgets())->toContain(FindingStatsOverview::class)
        ->and(FindingRecordResource::shouldSkipAuthorization())->toBeTrue();
});

it('can use the data health brand logo for a panel', function () {
    $panel = Panel::make()->id('test');

    DataHealthPlugin::make()
        ->brandLogo()
        ->register($panel);

    expect($panel->getBrandLogo()?->toHtml())
        ->toContain('Data Health')
        ->toContain('#F5828F')
        ->and($panel->getDarkModeBrandLogo()?->toHtml())
        ->toContain('Data Health')
        ->toContain('#F5828F')
        ->and($panel->getBrandLogoHeight())->toBe('2rem');
});

it('has a stable plugin identifier', function () {
    expect(DataHealthPlugin::make()->getId())->toBe('data-health');
});

it('merges the default package configuration', function () {
    expect(config('data-health-filament.navigation_group'))->toBe('Data Health')
        ->and(config('data-health-filament.navigation_sort'))->toBe(100)
        ->and(config('data-health-filament.polling_interval'))->toBeNull()
        ->and(config('data-health-filament.assignee_types'))->toBeNull()
        ->and(config('data-health-filament.model_view_routes'))->toBe([])
        ->and(config('data-health-filament.model_view_url_resolver'))->toBeNull();
});

it('registers standalone Livewire components without a panel plugin', function () {
    $factory = app('livewire.factory');

    expect($factory->resolveComponentClass('data-health-filament::finding-records-table'))
        ->toBe(StandaloneFindingRecordsTable::class)
        ->and($factory->resolveComponentClass('data-health-filament::finding-types-table'))
        ->toBe(StandaloneFindingTypesTable::class)
        ->and($factory->resolveComponentClass('data-health-filament::model-findings-table'))
        ->toBe(ModelFindingsTable::class);
});

it('renders the standalone finding types table without a panel', function () {
    Livewire::test('data-health-filament::finding-types-table')
        ->assertOk();
});

it('renders the standalone findings table without a panel', function () {
    (require dirname(__DIR__, 2).'/vendor/data-health/data-health/database/migrations/2026_01_01_000000_create_data_health_findings_table.php')->up();

    $model = new TestModel;
    $model->setAttribute($model->getKeyName(), 1);
    $model->exists = true;

    app()->instance(DataHealthManager::class, new class extends DataHealthManager
    {
        public function __construct() {}

        public function getFindingForRecord(FindingRecord $record): Finding
        {
            return new ActionableFinding(new TestModel, $record->context);
        }
    });
    DataHealth::clearResolvedInstance(DataHealthManager::class);

    $record = FindingRecord::query()->create([
        'status' => RecordStatus::Active,
        'key' => 'test-finding',
        'model_type' => $model->getMorphClass(),
        'model_id' => 1,
        'context' => [],
        'context_hash' => hash('sha256', 'test-finding'),
        'urgency' => FindingUrgency::NORMAL,
        'last_detected_at' => now(),
    ]);

    Livewire::test('data-health-filament::finding-records-table')
        ->assertOk()
        ->assertSee('test-finding')
        ->assertSee('Verify description from the finding.')
        ->assertSee('Resolve description from the finding.')
        ->assertSee('Ignore this single issue forever')
        ->assertTableFilterExists('status', fn (SelectFilter $filter): bool => $filter->getDefaultState() === RecordStatus::Active->value)
        ->assertTableColumnExists('urgency', fn (TextColumn $column): bool => $column->isToggleable())
        ->assertTableColumnExists('worklist', fn (TextColumn $column): bool => $column->isToggleable())
        ->assertTableColumnExists('last_detected_at', fn (TextColumn $column): bool => $column->isToggleable())
        ->assertTableActionExists('verify')
        ->assertTableActionExists('resolve')
        ->assertTableActionExists('assign')
        ->assertTableActionHidden('assign', $record)
        ->assertTableActionExists('ignore')
        ->assertTableActionVisible('verify', $record)
        ->assertTableActionVisible('resolve', $record)
        ->assertTableActionVisible('ignore', $record);

    config()->set('data-health-filament.assignee_types', TestAssigneeTypes::class);

    Schema::create('test_models', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->boolean('active')->default(true);
        $table->timestamps();
    });

    $assignee = TestModel::query()->create([
        'name' => 'Test assignee',
        'active' => true,
    ]);

    // Remove once the core package release containing assignee() is installed.
    FindingRecord::resolveRelationUsing(
        'assignee',
        fn (FindingRecord $record): MorphTo => $record->morphTo('assignee'),
    );

    Livewire::test('data-health-filament::finding-records-table')
        ->assertOk()
        ->assertTableActionVisible('assign', $record)
        ->callTableAction('assign', $record, data: [
            'assignee_type' => $assignee->getMorphClass(),
            'assignee_id' => $assignee->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh())
        ->assignee_type->toBe($assignee->getMorphClass())
        ->assignee_id->toBe($assignee->getKey());

    Livewire::test('data-health-filament::model-findings-table', ['model' => $model])
        ->assertOk()
        ->assertSee('test-finding')
        ->assertTableActionExists('verify')
        ->assertTableActionExists('resolve')
        ->assertTableActionExists('ignore')
        ->assertTableActionVisible('verify', $record)
        ->assertTableActionVisible('resolve', $record)
        ->assertTableActionVisible('ignore', $record);

    Schema::drop('data_health_findings');
    Schema::drop('test_models');
});
