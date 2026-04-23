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
            'is_active' => ['required', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:1000'],
            'access_note' => ['nullable', 'string', 'max:1000'],
            'assignment_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('is_active');

        $this->merge([
            'slug' => is_string($this->input('slug')) ? Str::slug($this->input('slug')) : $this->input('slug'),
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
            'name' => 'role name',
            'slug' => 'role slug',
            'is_active' => 'role status',
            'review_note' => 'review note',
            'access_note' => 'access note',
            'assignment_note' => 'assignment note',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This role slug is already in use.',
            'review_note.max' => 'Keep the review note under 1000 characters so the role workspace stays operator-friendly.',
            'access_note.max' => 'Keep the access note under 1000 characters so the role workspace stays operator-friendly.',
            'assignment_note.max' => 'Keep the assignment note under 1000 characters so the role workspace stays operator-friendly.',
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
