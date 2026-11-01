<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

/**
 * `POST /auth/forgot-password` (CDC §4.2.1, §6.2) : lien à usage unique valable 1 h.
 *
 * La réponse est **toujours** `202` avec le même message, que l'adresse existe ou non :
 * elle ne doit pas permettre d'énumérer les comptes.
 */
final class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        // L'email est unique sur toute la plateforme ; la recherche se fait hors du scope
        // multi-tenant, puis le contexte est posé pour que le broker retrouve le compte.
        $user = User::withoutTenantScope()->where('email', $request->email())->first();

        if ($user !== null) {
            app(TenantContext::class)->run(
                $user->tenant_id,
                fn () => Password::sendResetLink(['email' => $request->email()])
            );
        }

        return response()->json([
            'message' => 'Si cette adresse existe, un lien de réinitialisation vous a été envoyé.',
        ], 202);
    }
}
