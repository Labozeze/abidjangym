<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PaymentMode;
use App\Enums\UserRole;
use App\Models\Gym;
use App\Models\PaymentMethodSetting;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Jeu de données fictif de développement (docs/rapports/strategie-de-tests.md §5).
 *
 * Deux salles — « FitZone Test » et « Salle Témoin » — chacune avec un gérant et un
 * caissier, pour que les tests d'isolation puissent prouver qu'une salle ne voit rien de
 * l'autre. Aucune donnée réelle : téléphones en `+225 07 00 00 00 XX`, emails en
 * `@example.com`, noms volontairement factices.
 *
 * Chaque salle démarre avec **seules les espèces activées** (CDC §4.2.6, décision du
 * 01/11/2026), les autres moyens restant désactivés jusqu'à configuration.
 */
final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->gyms() as $definition) {
            $gym = Gym::query()->firstOrCreate(
                ['name' => $definition['name']],
                $definition
            );

            $this->seedGymUsers($gym);
            $this->seedPaymentMethods($gym);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function gyms(): array
    {
        return [
            [
                'name' => 'FitZone Test',
                'address' => 'Rue des Jardins, Cocody',
                'district' => 'Cocody',
                'city' => 'Abidjan',
                'phone' => '+2250700000001',
                'whatsapp' => '+2250700000002',
                'email' => 'fitzone@example.com',
                'timezone' => 'Africa/Abidjan',
                'currency' => 'XOF',
                'receipt_footer_message' => 'Merci de votre confiance !',
            ],
            [
                'name' => 'Salle Témoin',
                'address' => 'Boulevard Principal, Yopougon',
                'district' => 'Yopougon',
                'city' => 'Abidjan',
                'phone' => '+2250700000003',
                'whatsapp' => '+2250700000004',
                'email' => 'salle-temoin@example.com',
                'timezone' => 'Africa/Abidjan',
                'currency' => 'XOF',
                'receipt_footer_message' => 'Merci de votre confiance !',
            ],
        ];
    }

    private function seedGymUsers(Gym $gym): void
    {
        $slug = $gym->name === 'FitZone Test' ? 'fitzone' : 'salle-temoin';

        app(TenantContext::class)->run($gym->id, function () use ($slug): void {
            User::query()->updateOrCreate(
                ['email' => 'gerant@'.$slug.'.example.com'],
                [
                    'first_name' => 'Awa',
                    'last_name' => 'Démo',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Admin,
                    'is_active' => true,
                ]
            );

            User::query()->updateOrCreate(
                ['email' => 'caissier@'.$slug.'.example.com'],
                [
                    'first_name' => 'Koffi',
                    'last_name' => 'Démo',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Cashier,
                    'is_active' => true,
                ]
            );
        });
    }

    private function seedPaymentMethods(Gym $gym): void
    {
        app(TenantContext::class)->run($gym->id, function (): void {
            foreach (PaymentMode::cases() as $mode) {
                PaymentMethodSetting::query()->updateOrCreate(
                    ['method' => $mode],
                    [
                        'enabled' => $mode === PaymentMode::Cash,
                        'instructions' => null,
                    ]
                );
            }
        });
    }
}
