<?php

declare(strict_types=1);

namespace DataHealth\Filament\Tests\Fixtures;

use DataHealth\Attributes\Description;
use DataHealth\Contracts\CanResolve;
use DataHealth\Contracts\CanVerify;
use DataHealth\Finding;

#[Description('Description from the finding class.')]
class ActionableFinding extends Finding implements CanResolve, CanVerify
{
    #[Description('Resolve description from the finding.')]
    public function resolve(): bool
    {
        return true;
    }

    #[Description('Verify description from the finding.')]
    public function verify(): bool
    {
        return true;
    }
}
