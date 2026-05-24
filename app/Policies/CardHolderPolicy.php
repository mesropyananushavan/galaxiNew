<?php

namespace App\Policies;

use App\Models\CardHolder;
use App\Models\User;

class CardHolderPolicy
{
    public function update(User $user, CardHolder $cardHolder): bool
    {
        return $user->can('view', $cardHolder->shop);
    }
}
