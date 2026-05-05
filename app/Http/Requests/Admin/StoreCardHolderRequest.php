<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\UrlGenerator;

class StoreCardHolderRequest extends FormRequest
{
    protected $redirectRoute = 'admin.cardholders.index';

    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('is_active');

        $this->merge([
            'full_name' => is_string($this->input('full_name')) ? trim($this->input('full_name')) : $this->input('full_name'),
            'phone' => is_string($this->input('phone')) ? trim($this->input('phone')) : $this->input('phone'),
            'email' => is_string($this->input('email')) ? strtolower(trim($this->input('email'))) : $this->input('email'),
            'is_active' => match (true) {
                is_bool($status) => $status,
                is_string($status) => in_array(strtolower($status), ['1', 'true', 'on', 'yes'], true),
                is_int($status) => $status === 1,
                default => false,
            },
        ]);
    }

    public function attributes(): array
    {
        return [
            'shop_id' => 'shop',
            'full_name' => 'cardholder name',
            'phone' => 'phone number',
            'email' => 'email address',
            'is_active' => 'cardholder status',
            'review_note' => 'review note',
        ];
    }

    public function messages(): array
    {
        return [
            'review_note.max' => 'Keep the review note under 1000 characters so the holder workspace stays operator-friendly.',
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
