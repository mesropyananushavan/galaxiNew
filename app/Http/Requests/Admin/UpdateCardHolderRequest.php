<?php

namespace App\Http\Requests\Admin;

use App\Models\CardHolder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Validator;

class UpdateCardHolderRequest extends StoreCardHolderRequest
{
    public function authorize(): bool
    {
        $cardHolder = $this->route('cardholder');

        return $cardHolder instanceof CardHolder && ($this->user()?->can('update', $cardHolder) ?? false);
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $this->validateCurrentShopAccess(
            $validator,
            fn (): ?\App\Models\Shop => $this->route('cardholder') instanceof CardHolder ? $this->route('cardholder')->shop : null,
            'Choose a cardholder from a shop you can access before changing holder details in the Galaxy workspace.',
        );
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $cardholder = $this->route('cardholder');

        if ($cardholder !== null) {
            return $url->route('admin.cardholders.index', ['cardholder' => $cardholder], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
