<?php

namespace App\Http\Requests\Admin\Concerns;

trait NormalizesBooleanFormInputs
{
    protected function normalizeBooleanInput(mixed $value, bool $default = false): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_string($value) => in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true),
            is_int($value) => $value === 1,
            default => $default,
        };
    }
}
