<?php

declare(strict_types=1);

use DataHealth\Enums\RecordStatus;
use DataHealth\Filament\Support\FindingRecordAction;
use DataHealth\Filament\Tests\Fixtures\ActionableFinding;
use DataHealth\Filament\Tests\Fixtures\Models\TestModel;
use DataHealth\Finding;
use DataHealth\Models\FindingRecord;

it('marks, ignores, and reopens finding records', function () {
    $record = new FindingRecord(['status' => RecordStatus::Active]);

    FindingRecordAction::markAsResolved($record);

    expect($record->status)->toBe(RecordStatus::Resolved);

    FindingRecordAction::reopen($record);

    FindingRecordAction::ignore($record);

    expect($record->status)->toBe(RecordStatus::Ignored);

    FindingRecordAction::reopen($record);

    expect($record->status)->toBe(RecordStatus::Active);
});

it('uses method descriptions from attributes with a fallback', function () {
    $record = new class extends FindingRecord
    {
        public function getFinding(): Finding
        {
            return new ActionableFinding(new TestModel);
        }
    };

    expect(FindingRecordAction::methodDescription($record, 'verify', 'Fallback'))
        ->toBe('Verify description from the finding.')
        ->and(FindingRecordAction::methodDescription($record, 'missing', 'Fallback'))
        ->toBe('Fallback')
        ->and(FindingRecordAction::description($record))
        ->toBe('Description from the finding class.');
});
