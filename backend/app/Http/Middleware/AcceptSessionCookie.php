<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Auth\SessionTokens;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentifie le backoffice à partir du cookie `httpOnly` (CDC §6.2).
 *
 * Le cookie contient un jeton Sanctum opaque ; on le présente au guard sous la forme
 * attendue par Sanctum (`Authorization: Bearer …`). L'en-tête explicite reste prioritaire,
 * pour les clients API.
 */
final class AcceptSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->headers->has('Authorization')) {
            $token = $request->cookie(SessionTokens::ACCESS_COOKIE);

            if (is_string($token) && $token !== '') {
                $request->headers->set('Authorization', 'Bearer '.$token);
            }
        }

        return $next($request);
    }
}
