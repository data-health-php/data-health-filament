<?php

declare(strict_types=1);

namespace DataHealth\Filament\Support;

use DataHealth\Contracts\CanDetect;
use DataHealth\Finding;
use InvalidArgumentException;

final class FindingDetection
{
    /** @param class-string<Finding> $finding */
    public static function run(string $finding): int|bool|null
    {
        if (! is_a($finding, CanDetect::class, true)) {
            throw new InvalidArgumentException("{$finding} does not implement CanDetect");
        }

        $result = $finding::detect();

        return is_callable($result) ? app()->call($result) : $result;
    }
}
