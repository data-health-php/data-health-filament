<?php

declare(strict_types=1);

namespace DataHealth\Filament\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class TestModelViewUrlResolver
{
    public function __invoke(Model $model): string
    {
        return '/resolved-models/'.$model->getRouteKey();
    }
}
