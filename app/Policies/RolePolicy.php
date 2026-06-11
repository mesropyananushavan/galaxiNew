<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class RolePolicy
{
    use HandlesAdminPolicyAccess;

    public function viewAny(User $user): bool
    {
        return $this->canViewAdminIndex($user);
    }

    public function create(User $user): bool
    {
        return $this->canManageFoundationCatalog($user);
    }

    public function update(User $user, Role $role): bool
    {
        return $this->create($user);
    }
}
