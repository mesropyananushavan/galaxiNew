<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\Card;
use App\Models\Shop;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCardRequest extends StoreCardRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'card';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.cards.index';

    public function authorize(): bool
    {
        return $this->authorizeSelectedResourceUpdate(Card::class);
    }

    public function rules(): array
    {
        $card = $this->selectedResource();
        $cardId = is_object($card) && isset($card->id) ? $card->id : $card;

        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'card_holder_id' => ['nullable', 'integer', Rule::exists('card_holders', 'id')->where(fn ($query) => $query->where('shop_id', $this->input('shop_id')))],
            'card_type_id' => ['required', 'integer', 'exists:card_types,id'],
            'number' => ['required', 'string', 'max:255', Rule::unique('cards', 'number')->ignore($cardId)],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'blocked'])],
            'issued_at' => ['nullable', 'date', 'required_with:activated_at', 'required_if:status,blocked'],
            'activated_at' => ['nullable', 'date', 'required_if:status,active', 'prohibited_if:status,draft', 'after_or_equal:issued_at'],
            'review_note' => ['nullable', 'string', 'required_if:status,blocked', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $this->validateCurrentShopAccess(
            $validator,
            fn (): ?Shop => $this->selectedResource() instanceof Card ? $this->selectedResource()->shop : null,
            'Choose a card from a shop you can access before changing inventory details in the Galaxy workspace.',
        );
    }

}
