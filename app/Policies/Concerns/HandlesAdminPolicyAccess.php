<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait HandlesAdminPolicyAccess
{
    protected function canViewAdminIndex(User $user): bool
    {
        return $user->canAccessAdminPanel();
    }

    protected function canManageFoundationCatalog(User $user): bool
    {
        return $user->hasBootstrapAdminAccess();
    }
}
