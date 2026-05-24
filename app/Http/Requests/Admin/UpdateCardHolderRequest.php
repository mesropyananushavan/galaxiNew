<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\CardHolder;
use App\Models\Shop;
use Illuminate\Validation\Validator;

class UpdateCardHolderRequest extends StoreCardHolderRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'cardholder';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.cardholders.index';

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

}
