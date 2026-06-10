<?php

namespace App\Providers\Concerns;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

trait RegistersAdminAccessGates
{
    protected function registerAdminAccessGates(): void
    {
        Gate::define('access-admin', static function (User $user): bool {
            return $user->canAccessAdminPanel();
        });

        Gate::define('access-shop', static function (User $user, Shop $shop): bool {
            return $user->canAccessShop($shop);
        });

        Gate::define('view-gifts', static function (User $user): bool {
            return $user->canAccessAdminPanel();
        });

        Gate::define('view-reports', static function (User $user): bool {
            return $user->canAccessAdminPanel();
        });
    }
}
