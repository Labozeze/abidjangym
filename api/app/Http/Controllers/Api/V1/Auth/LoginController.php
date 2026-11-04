<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\Auth\SessionCookies;
use App\Support\Auth\SessionTokens;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * `POST /auth/login` (CDC §4.2.1, §6.2).
 *
 * Réponse conforme au contrat : utilisateur, jeton Bearer, expiration, et cookies
 * `httpOnly` pour le backoffice. Les identifiants secrets ne sont jamais journalisés.
 */
final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, SessionTokens $tokens): JsonResponse
    {
        // L'email est unique sur toute la plateforme (décision du 01/11/2026) : la
        // recherche se fait donc hors du scope multi-tenant.
        $user = User::withoutTenantScope()->where('email', $request->email())->first();

        if ($user === null || ! Hash::check($request->password(), $user->password)) {
            throw AuthenticationFailedException::invalidCredentials();
        }

        if (! $user->is_active) {
            throw AuthenticationFailedException::inactiveAccount();
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $issued = $tokens->issue($user, $request->rememberDevice());

        return response()
            ->json([
                'user' => new UserResource($user),
                'token_type' => 'Bearer',
                'access_token' => $issued->accessToken,
                'refresh_token' => $issued->refreshToken,
                'expires_in' => $issued->accessExpiresIn,
            ])
            ->withCookie(SessionCookies::access($issued->accessToken))
            ->withCookie(SessionCookies::refresh($issued->refreshToken, $issued->refreshLifetimeMinutes));
    }
}
