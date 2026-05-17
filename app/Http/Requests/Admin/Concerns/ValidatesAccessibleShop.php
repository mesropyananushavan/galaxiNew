<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Models\Shop;
use Closure;
use Illuminate\Validation\Validator;

trait ValidatesAccessibleShop
{
    protected function validateAccessibleShop(Validator $validator, string $message): void
    {
        $validator->after(function (Validator $validator) use ($message): void {
            $user = $this->user();
            $shopId = $this->input('shop_id');

            if ($validator->errors()->has('shop_id') || $user === null || blank($shopId)) {
                return;
            }

            $shop = Shop::query()->find($shopId);

            if ($shop instanceof Shop && ! $user->can('access-shop', $shop)) {
                $validator->errors()->add('shop_id', $message);
            }
        });
    }

    protected function validateCurrentShopAccess(Validator $validator, Closure $shopResolver, string $message): void
    {
        $validator->after(function (Validator $validator) use ($shopResolver, $message): void {
            $user = $this->user();
            $shop = $shopResolver();

            if ($user === null || ! $shop instanceof Shop) {
                return;
            }

            if (! $user->can('access-shop', $shop)) {
                $validator->errors()->add('shop_id', $message);
            }
        });
    }
}
