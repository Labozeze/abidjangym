<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\SanctumToken;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        $this->configureRateLimiters();
    }

    /**
     * Limiteurs nommés, **un compteur par route**.
     *
     * `throttle:5,1` sans nom partage un compteur entre toutes les routes qui ont les mêmes
     * paramètres : sur une IP partagée (box d'une salle, NAT d'opérateur), quelques essais du
     * formulaire de contact bloquaient la connexion du gérant — et inversement (relecture de
     * la PR #64).
     */
    private function configureRateLimiters(): void
    {
        // Connexion : 5 tentatives par minute et par IP (CDC §6.4).
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        // Mot de passe oublié et réinitialisation : ce sont des cibles de force brute.
        RateLimiter::for('password', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        // Rafraîchissement : jeton long et aléatoire, pas une surface de devinette. Limite
        // plus large pour qu'un bureau partageant une IP publique ne se bloque pas lui-même.
        RateLimiter::for('refresh', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        // Espace abonné : routes de lien magique à venir, limiteur déjà posé pour éviter
        // d'oublier la limite à leur implémentation.
        RateLimiter::for('magic-link', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        // Inscription d'une salle : formulaire en quatre étapes, donc 10 par minute pour
        // tolérer plusieurs erreurs de validation sans bloquer une salle légitime.
        RateLimiter::for('onboarding', fn (Request $request): Limit => Limit::perMinute(10)->by($request->ip()));

        // Formulaire de contact public : 5 par minute et par IP.
        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));
    }
}
