<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Alimente le `TenantContext` à partir de l'utilisateur authentifié (CDC §6.3).
 *
 * À brancher après l'authentification, sur toute route métier :
 *
 *     Route::middleware(['auth:sanctum', 'tenant'])->group(function (): void { ... });
 *
 * Sans utilisateur authentifié, le contexte est explicitement vidé : le scope multi-tenant
 * refuse alors par défaut (aucune ligne visible), plutôt que de laisser fuir les données
 * de la dernière requête.
 *
 * Le `finally` garantit que le contexte ne survit pas à la requête, même en cas d'exception
 * ou sous un serveur qui réutilise le processus (Octane, worker).
 */
final class ResolveTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(TenantContext::class);
        $user = $request->user();

        if ($user !== null && isset($user->tenant_id)) {
            $context->set((int) $user->tenant_id);
        } else {
            $context->forget();
        }

        try {
            return $next($request);
        } finally {
            $context->forget();
        }
    }
}
