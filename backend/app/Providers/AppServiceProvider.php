<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\SanctumToken;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Auth\Notifications\ResetPassword;
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

        /*
         * Lien de réinitialisation du mot de passe.
         *
         * La notification standard de Laravel construit son lien avec la route web
         * `password.reset`, qui n'existe pas dans une API : sans cette personnalisation,
         * l'envoi échoue en 500 (et révèle, par différence avec le 202 renvoyé pour une
         * adresse inconnue, quels comptes existent).
         *
         * Le lien pointe donc vers la page de réinitialisation du **backoffice**. Le
         * chemin de cet écran est `[À FOURNIR]` tant qu'il n'est pas développé
         * (`config/backoffice.php`).
         */
        ResetPassword::createUrlUsing(function (User $user, string $token): string {
            $base = rtrim((string) config('backoffice.url'), '/');
            $path = '/'.ltrim((string) config('backoffice.reset_path'), '/');

            return $base.$path.'?'.http_build_query([
                'token' => $token,
                'email' => $user->email,
            ]);
        });
    }
}
