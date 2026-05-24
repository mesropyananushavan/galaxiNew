<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Policies\CardHolderPolicy;
use App\Policies\CardPolicy;
use App\Policies\CardTypePolicy;
use App\Policies\RolePolicy;
use App\Policies\ShopPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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

        Gate::define('access-admin', static function (User $user): bool {
            return $user->canAccessAdminPanel();
        });

        Gate::define('access-shop', static function (User $user, Shop $shop): bool {
            return $user->canAccessShop($shop);
        });
    }
}
