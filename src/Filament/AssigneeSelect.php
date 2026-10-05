<?php

declare(strict_types=1);

namespace DataHealth\Filament\Filament;

use DataHealth\Filament\Support\AssigneeTypes;
use Filament\Forms\Components\MorphToSelect;

final class AssigneeSelect
{
    public static function make(): MorphToSelect
    {
        return MorphToSelect::make('assignee')
            ->types(fn (): array => app(AssigneeTypes::class)->all())
            ->searchable();
    }
}
