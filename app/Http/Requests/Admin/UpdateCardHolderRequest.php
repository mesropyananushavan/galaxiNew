<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\CardHolder;
use App\Models\Shop;
use Illuminate\Validation\Validator;

class UpdateCardHolderRequest extends StoreCardHolderRequest
{
    use AuthorizesPolicyActions;
    use ResolvesAdminSelectedResourceRedirects;

    public function authorize(): bool
    {
        return $this->authorizeUpdate('cardholder', CardHolder::class);
    }

    protected function selectedResourceRouteParameter(): string
    {
        return 'cardholder';
    }

    protected function selectedResourceRouteName(): string
    {
        return 'admin.cardholders.index';
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

}
