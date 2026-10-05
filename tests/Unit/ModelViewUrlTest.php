<?php

declare(strict_types=1);

use DataHealth\Filament\Support\ModelViewUrl;
use DataHealth\Filament\Tests\Fixtures\Models\TestModel;
use DataHealth\Filament\Tests\Fixtures\TestModelViewUrlResolver;
use DataHealth\Models\FindingRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

function findingRecordForModel(Model $model): FindingRecord
{
    $record = new FindingRecord;
    $record->model_type = $model->getMorphClass();
    $record->model_id = $model->getKey();
    $record->setRelation('model', $model);

    return $record;
}

it('prefers a model route mapping over the configured resolver', function () {
    Route::get('/test-models/{model}', fn (): string => '')->name('test-models.show');
    Route::getRoutes()->refreshNameLookups();

    $model = new TestModel;
    $model->setAttribute($model->getKeyName(), 123);
    $model->exists = true;

    $resolverWasCalled = false;

    config()->set('data-health-filament.model_view_routes', [
        TestModel::class => 'test-models.show',
    ]);
    config()->set(
        'data-health-filament.model_view_url_resolver',
        function (Model $model) use (&$resolverWasCalled): string {
            $resolverWasCalled = true;

            return '/resolver/'.$model->getRouteKey();
        },
    );

    expect(ModelViewUrl::resolve(findingRecordForModel($model)))
        ->toBe(route('test-models.show', [$model]))
        ->and($resolverWasCalled)->toBeFalse();
});

it('uses an invokable resolver when no model route is mapped', function () {
    $model = new TestModel;
    $model->setAttribute($model->getKeyName(), 456);
    $model->exists = true;

    config()->set('data-health-filament.model_view_routes', []);
    config()->set('data-health-filament.model_view_url_resolver', TestModelViewUrlResolver::class);

    expect(ModelViewUrl::resolve(findingRecordForModel($model)))
        ->toBe('/resolved-models/456');
});

it('returns no URL without a mapping, resolver, or current panel', function () {
    $model = new TestModel;
    $model->setAttribute($model->getKeyName(), 789);
    $model->exists = true;

    config()->set('data-health-filament.model_view_routes', []);
    config()->set('data-health-filament.model_view_url_resolver', null);

    expect(ModelViewUrl::resolve(findingRecordForModel($model)))->toBeNull();
});
