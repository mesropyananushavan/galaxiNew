<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Support\Str;

trait NormalizesSlugInputs
{
    protected function normalizeSlugInput(mixed $value): mixed
    {
        return is_string($value) ? Str::slug($value) : $value;
    }
}
