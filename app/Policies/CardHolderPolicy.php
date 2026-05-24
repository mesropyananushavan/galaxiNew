<?php

namespace App\Policies;

use App\Models\CardHolder;
use App\Models\User;

class CardHolderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessAdminPanel();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CardHolder $cardHolder): bool
    {
        return $user->can('view', $cardHolder->shop);
    }
}
