<?php

namespace App\Http\Requests\Admin\Concerns;

trait NormalizesTextFormInputs
{
    protected function normalizeTrimmedString(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }

    protected function normalizeNullableTrimmedString(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
