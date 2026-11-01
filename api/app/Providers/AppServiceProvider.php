<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Tenancy\TenantContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
         * Contexte multi-tenant : une instance par requête (scoped), jamais partagée
         * entre deux requêtes ou deux jobs. Une fuite de contexte serait une fuite de
         * données entre deux salles (CDC §6.3).
         */
        $this->app->scoped(TenantContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
