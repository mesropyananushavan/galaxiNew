<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessAdminPanel();
    }

    public function view(User $user, Shop $shop): bool
    {
        return $user->canAccessShop($shop);
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->canAccessShop($shop);
    }
}
