<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class CardPolicy
{
    use HandlesAdminPolicyAccess;

    public function viewAny(User $user): bool
    {
        return $this->canViewAdminIndex($user);
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
