<?php

namespace App\Http\Requests\Admin;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends StoreRoleRequest
{
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('roles', 'slug')->ignore($role),
            ],
        ];
    }

    protected function getRedirectUrl(): string
    {
        /** @var UrlGenerator $url */
        $url = $this->redirector->getUrlGenerator();
        $role = $this->route('role');

        if ($role !== null) {
            return $url->route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#live-form';
        }

        return parent::getRedirectUrl();
    }
}
