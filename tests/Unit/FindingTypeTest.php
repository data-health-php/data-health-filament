<?php

declare(strict_types=1);

use DataHealth\Filament\Support\FindingType;
use DataHealth\Filament\Tests\Fixtures\ActionableFinding;
use DataHealth\Filament\Tests\Fixtures\DetectableFinding;

it('describes the capabilities exposed by a finding class', function () {
    $detectable = FindingType::fromClass(DetectableFinding::class);
    $actionable = FindingType::fromClass(ActionableFinding::class);

    expect($detectable->key)->toBe('DetectableFinding')
        ->and($detectable->canDetect)->toBeTrue()
        ->and($detectable->canVerify)->toBeFalse()
        ->and($detectable->canResolve)->toBeFalse()
        ->and($actionable->canDetect)->toBeFalse()
        ->and($actionable->canVerify)->toBeTrue()
        ->and($actionable->canResolve)->toBeTrue();
});
