<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\Shop;

class UpdateShopRequest extends StoreShopRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'shop';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.shops.index';

    public function authorize(): bool
    {
        return $this->authorizeSelectedResourceUpdate(Shop::class);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                $this->uniqueRuleIgnoringSelectedResource('shops', 'code'),
            ],
        ]);
    }

}
