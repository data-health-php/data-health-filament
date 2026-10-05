<?php

declare(strict_types=1);

namespace DataHealth\Filament\Support;

use DataHealth\Contracts\CanResolve;
use DataHealth\Contracts\CanVerify;
use DataHealth\Enums\RecordStatus;
use DataHealth\Models\FindingRecord;
use Throwable;

final class FindingRecordAction
{
    public static function canVerify(FindingRecord $record): bool
    {
        try {
            return $record->getFinding() instanceof CanVerify;
        } catch (Throwable) {
            return false;
        }
    }

    public static function canResolve(FindingRecord $record): bool
    {
        try {
            return $record->getFinding() instanceof CanResolve;
        } catch (Throwable) {
            return false;
        }
    }

    public static function methodDescription(FindingRecord $record, string $method, string $fallback): string
    {
        try {
            return $record->getFinding()::getMethodDescription($method) ?? $fallback;
        } catch (Throwable) {
            return $fallback;
        }
    }

    public static function description(FindingRecord $record): ?string
    {
        try {
            return $record->getFinding()::getDescription();
        } catch (Throwable) {
            return null;
        }
    }

    public static function markAsResolved(FindingRecord $record): void
    {
        self::setStatus($record, RecordStatus::Resolved);
    }

    public static function ignore(FindingRecord $record): void
    {
        self::setStatus($record, RecordStatus::Ignored);
    }

    public static function reopen(FindingRecord $record): void
    {
        self::setStatus($record, RecordStatus::Active);
    }

    private static function setStatus(FindingRecord $record, RecordStatus $status): void
    {
        $record->status = $status;

        if ($record->exists) {
            $record->save();
        }
    }
}
