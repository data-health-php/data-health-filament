<?php

declare(strict_types=1);

namespace DataHealth\Filament\Support;

use Filament\Forms\Components\MorphToSelect\Type;
use InvalidArgumentException;
use UnexpectedValueException;

final class AssigneeTypes
{
    /** @return array<Type> */
    public function all(): array
    {
        $providerClass = config('data-health-filament.assignee_types');

        if ($providerClass === null) {
            return [];
        }

        if (! is_string($providerClass) || ! class_exists($providerClass)) {
            throw new InvalidArgumentException('The configured Data Health assignee type provider must be an existing class name.');
        }

        $provider = app($providerClass);

        if (! is_callable($provider)) {
            throw new InvalidArgumentException("The configured Data Health assignee type provider [{$providerClass}] must be invokable.");
        }

        $types = app()->call($provider);

        if (! is_array($types)) {
            throw new UnexpectedValueException("The configured Data Health assignee type provider [{$providerClass}] must return an array.");
        }

        foreach ($types as $type) {
            if (! $type instanceof Type) {
                throw new UnexpectedValueException("The configured Data Health assignee type provider [{$providerClass}] must only return MorphToSelect Type instances.");
            }
        }

        return array_values($types);
    }
}
