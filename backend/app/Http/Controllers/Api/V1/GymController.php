<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentMode;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGymRequest;
use App\Mail\GymRegisteredMail;
use App\Models\Gym;
use App\Models\PaymentMethodSetting;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * `POST /gyms` — inscription d'une salle (CDC §10.1, contrat v0.3.0).
 *
 * La création est **transactionnelle** : salle, compte gérant et réglages de paiement
 * sont écrits ensemble ou pas du tout. Un échec au milieu ne doit jamais laisser une
 * salle sans gérant, ni un gérant sans salle.
 *
 * L'email de confirmation part en différé (`defer()`) : la réponse ne dépend pas du SMTP,
 * et aucun worker n'est nécessaire (CDC §3.2).
 */
final class GymController extends Controller
{
    public function __invoke(StoreGymRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        [$gym, $admin] = DB::transaction(function () use ($data): array {
            $gym = Gym::create([
                'name' => $data['gym']['name'],
                'plan_code' => $data['plan_code'],
                'district' => $data['gym']['district'],
                'address' => $data['gym']['address'],
                'city' => 'Abidjan',
                'phone' => $data['gym']['phone'],
                'whatsapp' => $data['gym']['whatsapp'],
                'timezone' => 'Africa/Abidjan',
                'currency' => 'XOF',
            ]);

            $admin = app(TenantContext::class)->run(
                $gym->id,
                fn (): User => $this->createAdmin($data['admin'])
            );

            return [$gym, $admin];
        });

        $administrationUrl = $this->administrationUrl();

        defer(function () use ($gym, $admin, $administrationUrl): void {
            try {
                Mail::to($admin->email)->send(new GymRegisteredMail($gym, $admin, $administrationUrl));
            } catch (Throwable $exception) {
                // L'inscription est déjà enregistrée : un échec d'email ne doit pas la
                // remettre en cause, seulement être journalisé.
                report($exception);
            }
        });

        return response()->json([
            'data' => [
                'gym_id' => $gym->id,
                'administration_url' => $administrationUrl,
                'message' => 'Votre salle a été créée. Consultez votre email pour confirmer votre compte.',
            ],
        ], 201);
    }

    /**
     * Crée le gérant et les sept réglages de moyens de paiement.
     *
     * Décision de Rufin du 01/11/2026 : à l'inscription, seules les **espèces** sont
     * activées ; les autres moyens restent désactivés tant qu'ils ne sont pas configurés.
     *
     * @param  array<string, string>  $attributes
     */
    private function createAdmin(array $attributes): User
    {
        $admin = User::create([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        foreach (PaymentMode::cases() as $mode) {
            PaymentMethodSetting::create([
                'method' => $mode,
                'enabled' => $mode === PaymentMode::Cash,
            ]);
        }

        return $admin;
    }

    private function administrationUrl(): string
    {
        $base = rtrim((string) config('backoffice.url'), '/');
        $path = '/'.ltrim((string) config('backoffice.dashboard_path'), '/');

        return $base.$path;
    }
}
