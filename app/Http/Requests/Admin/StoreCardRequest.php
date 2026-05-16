<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ValidatesAccessibleShop;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCardRequest extends FormRequest
{
    use ValidatesAccessibleShop;

    protected $redirectRoute = 'admin.cards.index';

    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'card_holder_id' => ['nullable', 'integer', Rule::exists('card_holders', 'id')->where(fn ($query) => $query->where('shop_id', $this->input('shop_id')))],
            'card_type_id' => ['required', 'integer', 'exists:card_types,id'],
            'number' => ['required', 'string', 'max:255', Rule::unique('cards', 'number')],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'blocked'])],
            'issued_at' => ['nullable', 'date', 'required_with:activated_at', 'required_if:status,blocked'],
            'activated_at' => ['nullable', 'date', 'required_if:status,active', 'prohibited_if:status,draft', 'after_or_equal:issued_at'],
            'review_note' => ['nullable', 'string', 'required_if:status,blocked', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'number' => is_string($this->input('number')) ? strtoupper(trim($this->input('number'))) : $this->input('number'),
            'status' => is_string($this->input('status')) ? strtolower(trim($this->input('status'))) : $this->input('status'),
            'issued_at' => blank($this->input('issued_at')) ? null : $this->input('issued_at'),
            'activated_at' => blank($this->input('activated_at')) ? null : $this->input('activated_at'),
            'review_note' => is_string($this->input('review_note')) ? (trim($this->input('review_note')) !== '' ? trim($this->input('review_note')) : null) : $this->input('review_note'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'shop_id' => 'shop',
            'card_holder_id' => 'cardholder',
            'card_type_id' => 'card type',
            'number' => 'card number',
            'status' => 'card status',
            'issued_at' => 'issue timestamp',
            'activated_at' => 'activation timestamp',
            'review_note' => 'review note',
        ];
    }

    public function messages(): array
    {
        return [
            'card_holder_id.exists' => 'Choose a cardholder from the same shop so the Galaxy inventory shell keeps holder linkage scoped correctly.',
            'number.unique' => 'This card number is already in use in the Laravel inventory shell.',
            'issued_at.date' => 'Use a real issue timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            'issued_at.required_with' => 'Add an issue timestamp before activation so the Galaxy lifecycle timeline stays operator-friendly.',
            'issued_at.required_if' => 'Add an issue timestamp before blocking this card so the Galaxy lifecycle timeline stays operator-friendly.',
            'activated_at.date' => 'Use a real activation timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            'activated_at.required_if' => 'Add an activation timestamp before marking this card active so the Galaxy lifecycle timeline stays operator-friendly.',
            'activated_at.prohibited_if' => 'Leave activation blank while this card stays draft so the Galaxy lifecycle timeline stays operator-friendly.',
            'activated_at.after_or_equal' => 'Keep activation on or after issuance so the Galaxy lifecycle timeline stays operator-friendly.',
            'review_note.required_if' => 'Add a review note before blocking this card so the Galaxy inventory workspace stays operator-friendly.',
            'review_note.max' => 'Keep the review note under 1000 characters so the inventory workspace stays operator-friendly.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateAccessibleShop(
            $validator,
            'Choose a shop you can access so the Galaxy inventory shell stays scoped to your assigned branch.',
        );
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();

        if ($this->redirect) {
            return $url->to($this->redirect).'#live-form';
        }

        if ($this->redirectRoute) {
            return $url->route($this->redirectRoute).'#live-form';
        }

        if ($this->redirectAction) {
            return $url->action($this->redirectAction).'#live-form';
        }

        return $url->previous().'#live-form';
    }
}
