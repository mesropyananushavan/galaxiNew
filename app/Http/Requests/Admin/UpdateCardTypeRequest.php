<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\CardType;
use Illuminate\Validation\Rule;

class UpdateCardTypeRequest extends StoreCardTypeRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'cardType';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.card-types.index';

    public function authorize(): bool
    {
        return $this->authorizeUpdate('cardType', CardType::class);
    }

    public function rules(): array
    {
        $cardType = $this->route('cardType');

        return array_merge(parent::rules(), [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('card_types', 'slug')->ignore($cardType),
            ],
        ]);
    }

}
