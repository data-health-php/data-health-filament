<?php

declare(strict_types=1);

namespace DataHealth\Filament\Tests\Fixtures;

use DataHealth\Contracts\CanDetect;
use DataHealth\Finding;

class DetectableFinding extends Finding implements CanDetect
{
    public static function detect(): int
    {
        return 0;
    }
}
