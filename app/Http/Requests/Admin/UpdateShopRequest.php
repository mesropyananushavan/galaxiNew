<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\Shop;
use Illuminate\Validation\Rule;

class UpdateShopRequest extends StoreShopRequest
{
    use AuthorizesPolicyActions;
    use ResolvesAdminSelectedResourceRedirects;

    public function authorize(): bool
    {
        return $this->authorizeUpdate('shop', Shop::class);
    }

    protected function selectedResourceRouteParameter(): string
    {
        return 'shop';
    }

    protected function selectedResourceRouteName(): string
    {
        return 'admin.shops.index';
    }

    public function rules(): array
    {
        $shop = $this->route('shop');

        return array_merge(parent::rules(), [
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('shops', 'code')->ignore($shop),
            ],
        ]);
    }

}
