<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\NormalizesTextFormInputs;
use App\Http\Requests\Admin\Concerns\ResolvesAdminLiveFormRedirects;
use App\Http\Requests\Admin\Concerns\ValidatesAccessibleShop;
use App\Models\Card;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCardRequest extends FormRequest
{
    use AuthorizesPolicyActions;
    use NormalizesTextFormInputs;
    use ResolvesAdminLiveFormRedirects;
    use ValidatesAccessibleShop;

    protected $redirectRoute = 'admin.cards.index';

    public function authorize(): bool
    {
        return $this->authorizeCreate(Card::class);
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
            'number' => ($normalizedNumber = $this->normalizeTrimmedString($this->input('number'))) !== null && is_string($normalizedNumber)
                ? strtoupper($normalizedNumber)
                : $normalizedNumber,
            'status' => ($normalizedStatus = $this->normalizeTrimmedString($this->input('status'))) !== null && is_string($normalizedStatus)
                ? strtolower($normalizedStatus)
                : $normalizedStatus,
            'issued_at' => blank($this->input('issued_at')) ? null : $this->input('issued_at'),
            'activated_at' => blank($this->input('activated_at')) ? null : $this->input('activated_at'),
            'review_note' => $this->normalizeNullableTrimmedString($this->input('review_note')),
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
            'number.unique' => 'This card number is already in use in the Galaxy foundation inventory shell.',
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

}
