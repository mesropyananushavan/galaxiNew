<?php

namespace App\Providers\Concerns;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Role;
use App\Models\Shop;
use App\Policies\CardHolderPolicy;
use App\Policies\CardPolicy;
use App\Policies\CardTypePolicy;
use App\Policies\RolePolicy;
use App\Policies\ShopPolicy;
use Illuminate\Support\Facades\Gate;

trait RegistersAdminPolicies
{
    protected function registerAdminPolicies(): void
    {
        Gate::policy(Shop::class, ShopPolicy::class);
        Gate::policy(CardHolder::class, CardHolderPolicy::class);
        Gate::policy(Card::class, CardPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(CardType::class, CardTypePolicy::class);
    }
}
