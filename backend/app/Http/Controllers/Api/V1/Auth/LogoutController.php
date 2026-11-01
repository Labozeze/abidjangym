<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\SanctumToken;
use App\Support\Auth\SessionCookies;
use App\Support\Auth\SessionTokens;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * `POST /auth/logout` : révoque la session côté serveur (CDC §6.2) et efface les cookies.
 */
final class LogoutController extends Controller
{
    public function __invoke(Request $request, SessionTokens $tokens): Response
    {
        $user = $request->user();
        $current = $user?->currentAccessToken();

        if ($user !== null && $current instanceof SanctumToken) {
            $sessionId = $tokens->sessionIdOf($current->abilities);

            // Le jeton courant est supprimé en premier, puis le reste de la session
            // (jeton de rafraîchissement) — quel que soit son type.
            $current->delete();

            if ($sessionId !== null) {
                SanctumToken::query()
                    ->where('tokenable_id', $user->id)
                    ->where('tokenable_type', $user::class)
                    ->where('abilities', 'like', '%'.$sessionId.'%')
                    ->delete();
            }
        } elseif ($user !== null) {
            // Authentification par session (cookie de session PHP) : on révoque tout.
            $user->tokens()->delete();
        }

        return response()
            ->noContent()
            ->withCookie(SessionCookies::forgetAccess())
            ->withCookie(SessionCookies::forgetRefresh());
    }
}
