<?php

namespace App\Http\Requests\Admin;

use App\Models\Shop;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateShopRequest extends StoreShopRequest
{
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user();
            $shop = $this->route('shop');

            if ($user === null || ! $shop instanceof Shop) {
                return;
            }

            if (! $user->can('access-shop', $shop)) {
                $validator->errors()->add('shop_id', 'Choose a shop you can access before changing branch settings in the Galaxy workspace.');
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $shop = $this->route('shop');

        if ($shop !== null) {
            return $url->route('admin.shops.index', ['shop' => $shop], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
