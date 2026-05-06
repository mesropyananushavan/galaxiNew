<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;

class StoreCardRequest extends FormRequest
{
    protected $redirectRoute = 'admin.cards.index';

    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'card_type_id' => ['required', 'integer', 'exists:card_types,id'],
            'number' => ['required', 'string', 'max:255', Rule::unique('cards', 'number')],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'blocked'])],
            'issued_at' => ['nullable', 'date'],
            'activated_at' => ['nullable', 'date'],
            'review_note' => ['nullable', 'string', 'max:1000'],
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
            'number.unique' => 'This card number is already in use in the Laravel inventory shell.',
            'review_note.max' => 'Keep the review note under 1000 characters so the inventory workspace stays operator-friendly.',
        ];
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
