<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\AuthorizesPolicyActions;
use App\Http\Requests\Admin\Concerns\ResolvesAdminSelectedResourceRedirects;
use App\Models\Role;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends StoreRoleRequest
{
    use AuthorizesPolicyActions;
    use ResolvesAdminSelectedResourceRedirects;

    public function authorize(): bool
    {
        return $this->authorizeUpdate('role', Role::class);
    }

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

    protected function getRedirectUrl(): string
    {
        return $this->redirectToSelectedResource('role', 'admin.roles-permissions.index');
    }
}
