<?php

declare(strict_types=1);

namespace DataHealth\Filament\Tests\Fixtures;

use DataHealth\Filament\Tests\Fixtures\Models\TestModel;
use Filament\Forms\Components\MorphToSelect\Type;
use Illuminate\Database\Eloquent\Builder;

final class TestAssigneeTypes
{
    /** @return array<Type> */
    public function __invoke(): array
    {
        return [
            Type::make(TestModel::class)
                ->titleAttribute('name')
                ->modifyOptionsQueryUsing(fn (Builder $query): Builder => $query->where('active', true)),
        ];
    }
}
