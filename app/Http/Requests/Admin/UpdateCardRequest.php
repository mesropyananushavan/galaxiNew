<?php

namespace App\Http\Requests\Admin;

use App\Models\Card;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCardRequest extends StoreCardRequest
{
    public function authorize(): bool
    {
        $card = $this->route('card');

        return $card instanceof Card && ($this->user()?->can('update', $card) ?? false);
    }

    public function rules(): array
    {
        $card = $this->route('card');
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
            fn (): ?\App\Models\Shop => $this->route('card') instanceof Card ? $this->route('card')->shop : null,
            'Choose a card from a shop you can access before changing inventory details in the Galaxy workspace.',
        );
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $card = $this->route('card');

        if ($card !== null) {
            return $url->route('admin.cards.index', ['card' => $card], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
