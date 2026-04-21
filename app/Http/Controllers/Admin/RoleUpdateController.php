<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RedirectsToSelectedRoleContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;

class RoleUpdateController extends Controller
{
    use RedirectsToSelectedRoleContext;

    public function __invoke(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        return $this->redirectToSelectedRole(
            $role,
            sprintf('Role "%s" was updated.', $role->name),
        );
    }
}
