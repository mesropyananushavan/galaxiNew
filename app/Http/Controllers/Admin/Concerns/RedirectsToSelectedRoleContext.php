<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;

trait RedirectsToSelectedRoleContext
{
    protected function redirectToSelectedRole(Role $role, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->with('status', $status);
    }
}
