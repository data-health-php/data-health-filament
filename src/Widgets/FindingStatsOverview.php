<?php

declare(strict_types=1);

namespace DataHealth\Filament\Widgets;

use DataHealth\Enums\FindingUrgency;
use DataHealth\Enums\RecordStatus;
use DataHealth\Models\FindingRecord;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FindingStatsOverview extends StatsOverviewWidget
{
    protected function getPollingInterval(): ?string
    {
        return config('data-health-filament.polling_interval');
    }

    /** @return array<Stat> */
    protected function getStats(): array
    {
        $active = FindingRecord::query()->where('status', RecordStatus::Active->value);

        return [
            Stat::make('Active findings', (clone $active)->count())->color('danger'),
            Stat::make('Immediate', (clone $active)->where('urgency', FindingUrgency::IMMEDIATE->value)->count())->color('danger'),
            Stat::make('Resolved', FindingRecord::query()->where('status', RecordStatus::Resolved->value)->count())->color('success'),
            Stat::make('Ignored', FindingRecord::query()->where('status', RecordStatus::Ignored->value)->count())->color('gray'),
        ];
    }
}
