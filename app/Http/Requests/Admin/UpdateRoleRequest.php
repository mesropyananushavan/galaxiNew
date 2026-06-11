<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\Role;

class UpdateRoleRequest extends StoreRoleRequest
{
    use ResolvesAdminSelectedResourceRedirects;

    protected const SELECTED_RESOURCE_ROUTE_PARAMETER = 'role';
    protected const SELECTED_RESOURCE_ROUTE_NAME = 'admin.roles-permissions.index';

    public function authorize(): bool
    {
        return $this->authorizeSelectedResourceUpdate(Role::class);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                $this->uniqueRuleIgnoringSelectedResource('roles', 'slug'),
            ],
        ]);
    }

}
