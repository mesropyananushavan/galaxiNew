<?php

namespace App\Policies;

use App\Models\CardType;
use App\Models\User;

class CardTypePolicy
{
    public function create(User $user): bool
    {
        return $user->hasBootstrapAdminAccess();
    }

    public function update(User $user, CardType $cardType): bool
    {
        return $user->hasBootstrapAdminAccess();
    }
}
