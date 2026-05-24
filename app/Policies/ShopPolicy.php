<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class ShopPolicy
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

    public function view(User $user, Shop $shop): bool
    {
        return $user->canAccessShop($shop);
    }

    public function update(User $user, Shop $shop): bool
    {
        return $this->view($user, $shop);
    }
}
