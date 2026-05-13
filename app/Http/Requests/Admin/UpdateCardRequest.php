<?php

namespace App\Http\Requests\Admin;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;

class UpdateCardRequest extends StoreCardRequest
{
    public function rules(): array
    {
        $card = $this->route('card');
        $cardId = is_object($card) && isset($card->id) ? $card->id : $card;

        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'card_type_id' => ['required', 'integer', 'exists:card_types,id'],
            'number' => ['required', 'string', 'max:255', Rule::unique('cards', 'number')->ignore($cardId)],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'blocked'])],
            'issued_at' => ['nullable', 'date', 'required_with:activated_at', 'required_if:status,blocked'],
            'activated_at' => ['nullable', 'date', 'required_if:status,active', 'prohibited_if:status,draft', 'after_or_equal:issued_at'],
            'review_note' => ['nullable', 'string', 'required_if:status,blocked', 'max:1000'],
        ];
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
