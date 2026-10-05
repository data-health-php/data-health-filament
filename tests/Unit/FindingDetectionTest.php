<?php

declare(strict_types=1);

use DataHealth\Filament\Support\FindingDetection;
use DataHealth\Filament\Tests\Fixtures\CallableDetectableFinding;
use DataHealth\Filament\Tests\Fixtures\DetectableFinding;
use DataHealth\Filament\Tests\Fixtures\NonDetectableFinding;

it('runs immediate and deferred detection results', function () {
    expect(FindingDetection::run(DetectableFinding::class))->toBe(0)
        ->and(FindingDetection::run(CallableDetectableFinding::class))->toBeTrue();
});

it('rejects findings without detection support', function () {
    FindingDetection::run(NonDetectableFinding::class);
})->throws(InvalidArgumentException::class, 'does not implement CanDetect');
