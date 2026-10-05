<?php

declare(strict_types=1);

namespace DataHealth\Filament\Resources\FindingRecords\Pages;

use DataHealth\Filament\Resources\FindingRecords\FindingRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListFindingRecords extends ListRecords
{
    protected static string $resource = FindingRecordResource::class;
}
