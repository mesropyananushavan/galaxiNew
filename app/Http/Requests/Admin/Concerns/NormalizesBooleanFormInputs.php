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

    protected function normalizeFilterBooleanInput(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }
}
