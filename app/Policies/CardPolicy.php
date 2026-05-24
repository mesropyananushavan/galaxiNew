<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;

class CardPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessAdminPanel();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Card $card): bool
    {
        return $user->can('view', $card->shop);
    }
}
