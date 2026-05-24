<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\CardType;

class UpdateCardTypeRequest extends StoreCardTypeRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'cardType';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.card-types.index';

    public function authorize(): bool
    {
        return $this->authorizeSelectedResourceUpdate(CardType::class);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                $this->uniqueRuleIgnoringSelectedResource('card_types', 'slug'),
            ],
        ]);
    }

}
