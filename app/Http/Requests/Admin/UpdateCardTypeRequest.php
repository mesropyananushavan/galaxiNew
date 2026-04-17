<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateCardTypeRequest extends StoreCardTypeRequest
{
    public function rules(): array
    {
        $cardType = $this->route('cardType');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('card_types', 'slug')->ignore($cardType),
            ],
            'points_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
