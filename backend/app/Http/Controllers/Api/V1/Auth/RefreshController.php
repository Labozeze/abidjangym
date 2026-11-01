<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Http\Controllers\Controller;
use App\Models\SanctumToken;
use App\Models\User;
use App\Support\Auth\SessionCookies;
use App\Support\Auth\SessionTokens;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * `POST /auth/refresh` : émet un nouveau jeton d'accès tant que la session le permet
 * (plafond de 8 h, ou 30 jours avec « se souvenir de cet appareil »).
 */
final class RefreshController extends Controller
{
    public function __invoke(Request $request, SessionTokens $tokens): JsonResponse
    {
        $plain = $request->input('refresh_token') ?? $request->cookie(SessionTokens::REFRESH_COOKIE);

        if (! is_string($plain) || $plain === '') {
            throw AuthenticationFailedException::invalidRefreshToken();
        }

        // `SanctumToken` (modèle configuré) et non `PersonalAccessToken` : seul le premier
        // lève le scope multi-tenant sur la relation `tokenable`.
        $refreshToken = SanctumToken::findToken($plain);
        $user = $refreshToken?->tokenable;

        if (! $user instanceof User) {
            throw AuthenticationFailedException::invalidRefreshToken();
        }

        if (! $user->is_active) {
            throw AuthenticationFailedException::inactiveAccount();
        }

        $issued = $tokens->refresh($user, $refreshToken);

        return response()
            ->json([
                'token_type' => 'Bearer',
                'access_token' => $issued->accessToken,
                'refresh_token' => $issued->refreshToken,
                'expires_in' => $issued->accessExpiresIn,
            ])
            ->withCookie(SessionCookies::access($issued->accessToken))
            ->withCookie(SessionCookies::refresh($issued->refreshToken, $issued->refreshLifetimeMinutes));
    }
}
