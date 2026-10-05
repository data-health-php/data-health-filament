<?php

declare(strict_types=1);

namespace DataHealth\Filament\Support;

use DataHealth\Attributes\Async;
use DataHealth\Attributes\AutoResolve;
use DataHealth\Attributes\Scheduled;
use DataHealth\Contracts\CanDetect;
use DataHealth\Contracts\CanResolve;
use DataHealth\Contracts\CanVerify;
use DataHealth\Finding;
use ReflectionClass;

final readonly class FindingType
{
    /**
     * @param  class-string<Finding>  $class
     */
    private function __construct(
        public string $key,
        public string $class,
        public ?string $description,
        public ?string $worklist,
        public ?string $urgency,
        public bool $canDetect,
        public bool $canVerify,
        public bool $canResolve,
        public bool $isScheduled,
        public bool $isAsync,
        public bool $isAutoResolved,
    ) {}

    /** @param class-string<Finding> $class */
    public static function fromClass(string $class): self
    {
        $reflection = new ReflectionClass($class);

        return new self(
            key: $class::key(),
            class: $class,
            description: $class::getDescription(),
            worklist: $class::getWorklist(),
            urgency: $class::getUrgency()?->value,
            canDetect: is_a($class, CanDetect::class, true),
            canVerify: is_a($class, CanVerify::class, true),
            canResolve: is_a($class, CanResolve::class, true),
            isScheduled: $reflection->getAttributes(Scheduled::class) !== [],
            isAsync: $reflection->getAttributes(Async::class) !== [],
            isAutoResolved: $reflection->getAttributes(AutoResolve::class) !== [],
        );
    }

    /** @return array<string, bool|string|null> */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
