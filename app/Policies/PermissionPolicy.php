<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class PermissionPolicy
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

    public function update(User $user, Permission $permission): bool
    {
        return $this->create($user);
    }
}
