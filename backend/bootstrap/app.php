<?php

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Http\Middleware\AcceptSessionCookie;
use App\Http\Middleware\ResolveTenantContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Le backoffice s'authentifie par un cookie `httpOnly` : ce middleware le traduit en
         * en-tête `Authorization` avant le guard Sanctum. Il passe avant tout middleware de
         * route, l'en-tête explicite restant prioritaire pour les clients API.
         */
        $middleware->api(prepend: [
            AcceptSessionCookie::class,
        ]);

        /*
         * Alias `tenant` : à appliquer après l'authentification, sur les routes métier
         * (ex. ['auth:sanctum', 'tenant']).
         */
        $middleware->alias([
            'tenant' => ResolveTenantContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Échecs d'authentification : 401 avec un message en français, jamais de détail
        // permettant d'énumérer les comptes.
        $exceptions->render(
            fn (AuthenticationFailedException $e) => response()->json(['message' => $e->getMessage()], 401)
        );
    })->create();
