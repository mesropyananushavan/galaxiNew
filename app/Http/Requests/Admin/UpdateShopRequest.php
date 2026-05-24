<?php

namespace App\Http\Requests\Admin;

use App\Models\Shop;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;

class UpdateShopRequest extends StoreShopRequest
{
    public function authorize(): bool
    {
        $shop = $this->route('shop');

        return $shop instanceof Shop && ($this->user()?->can('update', $shop) ?? false);
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
