<?php

namespace App\Providers;

use App\Providers\Concerns\RegistersAdminAccessGates;
use App\Providers\Concerns\RegistersAdminPolicies;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use RegistersAdminAccessGates;
    use RegistersAdminPolicies;
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
        $this->registerAdminPolicies();
        $this->registerAdminAccessGates();
    }
}
