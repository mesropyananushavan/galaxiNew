<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class StoreRoleRequest extends FormRequest
{
    protected $redirectRoute = 'admin.roles-permissions.index';

    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,slug'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => is_string($this->input('slug')) ? Str::slug($this->input('slug')) : $this->input('slug'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'role name',
            'slug' => 'role slug',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This role slug is already in use.',
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
