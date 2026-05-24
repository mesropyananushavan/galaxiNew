<?php

namespace App\Policies;

use App\Models\CardType;
use App\Models\User;
use App\Policies\Concerns\HandlesAdminPolicyAccess;

class CardTypePolicy
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

    public function update(User $user, CardType $cardType): bool
    {
        return $this->create($user);
    }
}
