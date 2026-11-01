<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\SanctumToken;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        /*
         * Jetons Sanctum : le modèle personnalisé lève le scope multi-tenant sur la
         * relation `tokenable`, sinon l'authentification par jeton ne trouverait jamais
         * l'utilisateur (le contexte n'existe pas encore à ce moment-là).
         */
        Sanctum::usePersonalAccessTokenModel(SanctumToken::class);
    }
}
