<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * `POST /auth/reset-password` (CDC §6.2) : consomme un jeton à usage unique et révoque
 * toutes les sessions existantes du compte.
 */
final class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request): Response
    {
        $user = User::withoutTenantScope()->where('email', $request->email())->first();

        $status = $user === null
            ? Password::INVALID_USER
            : app(TenantContext::class)->run($user->tenant_id, fn (): string => Password::reset(
                [
                    'email' => $request->email(),
                    'password' => (string) $request->string('password'),
                    'password_confirmation' => (string) $request->string('password_confirmation'),
                    'token' => (string) $request->string('token'),
                ],
                function (User $user, string $password): void {
                    $user->forceFill(['password' => $password])->save();

                    // Un mot de passe changé invalide toutes les sessions ouvertes.
                    $user->tokens()->delete();
                }
            ));

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => $status === Password::RESET_THROTTLED
                    ? 'Trop de tentatives. Réessayez dans une minute.'
                    : 'Ce lien de réinitialisation est invalide ou a expiré.',
            ]);
        }

        return response()->noContent();
    }
}
