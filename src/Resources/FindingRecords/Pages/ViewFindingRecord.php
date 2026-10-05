<?php

declare(strict_types=1);

namespace DataHealth\Filament\Resources\FindingRecords\Pages;

use DataHealth\Filament\Filament\FindingActions;
use DataHealth\Filament\Resources\FindingRecords\FindingRecordResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewFindingRecord extends ViewRecord
{
    protected static string $resource = FindingRecordResource::class;

    /** @return array<Action> */
    protected function getHeaderActions(): array
    {
        return [
            FindingActions::assign(),
            FindingActions::reopen(),
        ];
    }
}
