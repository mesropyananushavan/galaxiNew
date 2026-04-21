<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedRoleContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;

class RoleStoreController extends Controller
{
    use RedirectsToSelectedRoleContext;

    public function __invoke(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create($request->validated());

        return $this->redirectToSelectedRole(
            $role,
            sprintf('Role "%s" was created.', $role->name),
        );
    }
}
