<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Models\CardHolder;
use App\Models\Shop;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Validator;

class UpdateCardHolderRequest extends StoreCardHolderRequest
{
    use AuthorizesPolicyActions;

    public function authorize(): bool
    {
        return $this->authorizeUpdate('cardholder', CardHolder::class);
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $this->validateCurrentShopAccess(
            $validator,
            fn (): ?Shop => $this->route('cardholder') instanceof CardHolder ? $this->route('cardholder')->shop : null,
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
