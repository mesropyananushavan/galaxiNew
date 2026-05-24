<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class StoreShopRequest extends FormRequest
{
    protected $redirectRoute = 'admin.shops.index';

    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:shops,code'],
            'is_active' => ['required', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('is_active');

        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'code' => is_string($this->input('code')) ? Str::slug($this->input('code')) : $this->input('code'),
            'review_note' => is_string($this->input('review_note')) ? (trim($this->input('review_note')) !== '' ? trim($this->input('review_note')) : null) : $this->input('review_note'),
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
            'name' => 'shop name',
            'code' => 'shop code',
            'is_active' => 'shop status',
            'review_note' => 'review note',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This shop code is already in use.',
            'review_note.max' => 'Keep the review note under 1000 characters so the branch workspace stays operator-friendly.',
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
