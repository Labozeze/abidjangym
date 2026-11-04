<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Throwable;

/**
 * `POST /auth/forgot-password` (CDC §4.2.1, §6.2) : lien à usage unique valable 1 h.
 *
 * La réponse est **toujours** `202` avec le même message : adresse inconnue, adresse
 * existante, ou échec d'envoi du message (SMTP indisponible, par exemple). Toute autre
 * réponse — un `500` notamment — révélerait quels comptes existent (CDC §6.4).
 */
final class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // L'email est unique sur toute la plateforme ; la recherche se fait hors du
            // scope multi-tenant, puis le contexte est posé pour que le broker retrouve
            // le compte.
            $user = User::withoutTenantScope()->where('email', $request->email())->first();

            if ($user !== null) {
                app(TenantContext::class)->run(
                    $user->tenant_id,
                    fn () => Password::sendResetLink(['email' => $request->email()])
                );
            }
        } catch (Throwable $exception) {
            // Échec d'envoi : journalisé côté serveur (handler d'exceptions), jamais
            // exposé au client. La réponse reste identique aux autres cas.
            report($exception);
        }

        return response()->json([
            'message' => 'Si cette adresse existe, un lien de réinitialisation vous a été envoyé.',
        ], 202);
    }
}
