<?php

declare(strict_types=1);

use DataHealth\Filament\Support\AssigneeTypes;
use DataHealth\Filament\Tests\Fixtures\Models\TestModel;
use DataHealth\Filament\Tests\Fixtures\TestAssigneeTypes;

it('resolves assignee types from the configured provider', function () {
    config()->set('data-health-filament.assignee_types', TestAssigneeTypes::class);

    $types = app(AssigneeTypes::class)->all();

    expect($types)->toHaveCount(1)
        ->and($types[0]->getModel())->toBe(TestModel::class)
        ->and($types[0]->getTitleAttribute())->toBe('name');
});

it('has no assignee types when no provider is configured', function () {
    config()->set('data-health-filament.assignee_types', null);

    expect(app(AssigneeTypes::class)->all())->toBe([]);
});

it('rejects a non-invokable assignee type provider', function () {
    config()->set('data-health-filament.assignee_types', TestModel::class);

    app(AssigneeTypes::class)->all();
})->throws(InvalidArgumentException::class, 'must be invokable');
