<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function create(User $user): bool
    {
        return $user->hasBootstrapAdminAccess();
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasBootstrapAdminAccess();
    }
}
