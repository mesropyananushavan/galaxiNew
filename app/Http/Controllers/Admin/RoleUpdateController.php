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
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $request->boolean('is_active'),
            'review_note' => $validated['review_note'] ?? null,
            'access_note' => $validated['access_note'] ?? null,
            'assignment_note' => $validated['assignment_note'] ?? null,
        ]);

        return $this->redirectToSelectedRole(
            $role,
            sprintf('Role "%s" was updated.', $role->name),
        );
    }
}
