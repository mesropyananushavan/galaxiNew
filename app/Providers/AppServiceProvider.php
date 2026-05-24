<?php

namespace App\Providers;

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
use App\Providers\Concerns\RegistersAdminAccessGates;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use RegistersAdminAccessGates;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Shop::class, ShopPolicy::class);
        Gate::policy(CardHolder::class, CardHolderPolicy::class);
        Gate::policy(Card::class, CardPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(CardType::class, CardTypePolicy::class);

        $this->registerAdminAccessGates();
    }
}
