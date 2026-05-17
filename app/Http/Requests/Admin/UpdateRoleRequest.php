<?php

namespace App\Http\Requests\Admin;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRoleRequest extends StoreRoleRequest
{
    public function rules(): array
    {
        $role = $this->route('role');

        return array_merge(parent::rules(), [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('roles', 'slug')->ignore($role),
            ],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateBootstrapAdminAccess(
            $validator,
            'slug',
            'Only bootstrap admins can update roles while the Galaxy access foundation is still centrally controlled.'
        );
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
