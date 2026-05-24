<?php

namespace App\Policies;

use App\Models\CardHolder;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class CardHolderPolicy
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

    public function update(User $user, CardHolder $cardHolder): bool
    {
        return $user->can('view', $cardHolder->shop);
    }
}
