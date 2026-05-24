<?php

namespace App\Http\Requests\Admin\Concerns;

trait NormalizesTextFormInputs
{
    protected function normalizeTrimmedString(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }

    protected function normalizeUpperTrimmedString(mixed $value): mixed
    {
        $normalizedValue = $this->normalizeTrimmedString($value);

        return is_string($normalizedValue) ? strtoupper($normalizedValue) : $normalizedValue;
    }

    protected function normalizeLowerTrimmedString(mixed $value): mixed
    {
        $normalizedValue = $this->normalizeTrimmedString($value);

        return is_string($normalizedValue) ? strtolower($normalizedValue) : $normalizedValue;
    }

    protected function normalizeNullableTrimmedString(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }

    protected function normalizeNullableLowerTrimmedString(mixed $value): mixed
    {
        $normalizedValue = $this->normalizeNullableTrimmedString($value);

        return is_string($normalizedValue) ? strtolower($normalizedValue) : $normalizedValue;
    }
}
