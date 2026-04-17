<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCardTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:card_types,slug'],
            'points_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => is_string($this->input('slug')) ? Str::slug($this->input('slug')) : $this->input('slug'),
            'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ?? $this->input('is_active'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'card type name',
            'slug' => 'card type slug',
            'points_rate' => 'points rate',
            'is_active' => 'status',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This card type slug is already in use.',
            'is_active.boolean' => 'The status field must be Active or Draft.',
        ];
    }
}
