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
        // L'email est unique sur toute la plateforme : la recherche se fait hors du scope
        // multi-tenant, rapidement, et ne dépend pas de l'existence d'un compte.
        $user = User::withoutTenantScope()->where('email', $request->email())->first();

        if ($user !== null) {
            /*
             * Envoi **différé** : la réponse ne doit pas dépendre du SMTP. Sans cela, le
             * temps de réponse — et non plus le code — révèle quels comptes existent
             * (1,9 s contre 0,15 s relevé en relecture de la PR #62).
             *
             * Le contexte multi-tenant est reposé ici : au moment où le callback
             * s'exécute, la requête est terminée et le contexte a été vidé.
             */
            defer(function () use ($user): void {
                try {
                    app(TenantContext::class)->run(
                        $user->tenant_id,
                        fn () => Password::sendResetLink(['email' => $user->email])
                    );
                } catch (Throwable $exception) {
                    // Échec d'envoi : journalisé côté serveur, jamais exposé au client.
                    report($exception);
                }
            });
        }

        return response()->json([
            'message' => 'Si cette adresse existe, un lien de réinitialisation vous a été envoyé.',
        ], 202);
    }
}
