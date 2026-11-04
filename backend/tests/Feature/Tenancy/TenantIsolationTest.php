<?php

declare(strict_types=1);

namespace Tests\Feature\Tenancy;

use App\Enums\PaymentMode;
use App\Enums\UserRole;
use App\Exceptions\Tenancy\MissingTenantContextException;
use App\Exceptions\Tenancy\TenantMismatchException;
use App\Models\Gym;
use App\Models\PaymentMethodSetting;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prouve l'isolation multi-tenant exigée par le CDC §6.3 et backend/AGENTS.md §1 :
 * une salle ne voit ni ne modifie les données d'une autre.
 *
 * Les vrais modèles sont utilisés (`User`, `PaymentMethodSetting`) : `ExampleRecord` a été
 * supprimé avec le ticket #39.
 */
final class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private TenantContext $context;

    private Gym $gymA;

    private Gym $gymB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = app(TenantContext::class);
        $this->gymA = Gym::factory()->create(['name' => 'FitZone Test']);
        $this->gymB = Gym::factory()->create(['name' => 'Salle Témoin']);
    }

    protected function tearDown(): void
    {
        $this->context->forget();

        parent::tearDown();
    }

    public function test_users_are_scoped_to_the_current_tenant(): void
    {
        $fromA = $this->createUser($this->gymA, 'awa@example.com');
        $fromB = $this->createUser($this->gymB, 'koffi@example.com');

        $this->assertSame($this->gymA->id, $fromA->tenant_id);
        $this->assertSame($this->gymB->id, $fromB->tenant_id);

        $this->context->run($this->gymA->id, function () use ($fromB): void {
            $this->assertSame(['awa@example.com'], User::query()->pluck('email')->all());
            $this->assertNull(
                User::query()->find($fromB->id),
                'La salle A ne doit pas voir l’utilisateur de la salle B.'
            );
        });

        $this->context->run($this->gymB->id, function () use ($fromA): void {
            $this->assertSame(['koffi@example.com'], User::query()->pluck('email')->all());
            $this->assertNull(User::query()->find($fromA->id));
        });
    }

    public function test_payment_method_settings_are_scoped_to_the_current_tenant(): void
    {
        $cashA = $this->createMethod($this->gymA, PaymentMode::Cash, true);
        $waveB = $this->createMethod($this->gymB, PaymentMode::Wave, true);

        $this->context->run($this->gymA->id, function () use ($waveB): void {
            $this->assertSame(
                [PaymentMode::Cash],
                PaymentMethodSetting::query()->get()->map(fn (PaymentMethodSetting $s): PaymentMode => $s->method)->all()
            );
            $this->assertNull(PaymentMethodSetting::query()->find($waveB->id));
        });

        $this->context->run($this->gymB->id, function () use ($cashA): void {
            $this->assertSame(
                [PaymentMode::Wave],
                PaymentMethodSetting::query()->get()->map(fn (PaymentMethodSetting $s): PaymentMode => $s->method)->all()
            );
            $this->assertNull(PaymentMethodSetting::query()->find($cashA->id));
        });
    }

    public function test_the_email_is_unique_across_the_platform(): void
    {
        $this->createUser($this->gymA, 'contact@example.com');

        $this->expectException(QueryException::class);

        // Décision du 01/11/2026 : un email ne peut appartenir qu'à une seule salle.
        $this->createUser($this->gymB, 'contact@example.com');
    }

    public function test_without_tenant_context_no_row_is_visible(): void
    {
        $this->createUser($this->gymA, 'awa@example.com');
        $this->createMethod($this->gymA, PaymentMode::Cash, true);

        $this->assertSame(
            0,
            User::query()->count(),
            'Sans salle courante, le scope doit renvoyer zéro ligne (fail closed).'
        );
        $this->assertSame(0, PaymentMethodSetting::query()->count());
    }

    public function test_creating_without_tenant_context_is_refused(): void
    {
        $this->expectException(MissingTenantContextException::class);

        User::factory()->create();
    }

    public function test_writing_into_another_tenant_is_refused(): void
    {
        $this->expectException(TenantMismatchException::class);

        $this->context->run($this->gymA->id, function (): void {
            $user = new User([
                'first_name' => 'Intrus',
                'last_name' => 'Test',
                'email' => 'intrus@example.com',
                'password' => 'password',
                'role' => UserRole::Cashier,
            ]);

            $user->forceFill(['tenant_id' => $this->gymB->id])->save();
        });
    }

    public function test_changing_tenant_on_update_is_refused(): void
    {
        $user = $this->createUser($this->gymA, 'awa@example.com');

        $this->expectException(TenantMismatchException::class);

        $this->context->run($this->gymA->id, function () use ($user): void {
            $user->forceFill(['tenant_id' => $this->gymB->id])->save();
        });
    }

    public function test_tenant_id_cannot_be_mass_assigned(): void
    {
        // `new User([...])` respecte `$fillable` (contrairement aux factories, qui créent
        // sous `Model::unguarded`) : `tenant_id` doit donc être ignoré.
        $user = $this->context->run($this->gymA->id, function (): User {
            $user = new User([
                'first_name' => 'Masse',
                'last_name' => 'Test',
                'email' => 'masse@example.com',
                'password' => 'password',
                'role' => UserRole::Cashier,
                'tenant_id' => $this->gymB->id,
            ]);

            $user->save();

            return $user;
        });

        $this->assertSame(
            $this->gymA->id,
            $user->tenant_id,
            'tenant_id doit être ignoré par l’assignation de masse et rempli par le contexte.'
        );
    }

    public function test_maintenance_can_bypass_the_scope_explicitly(): void
    {
        $this->createUser($this->gymA, 'a@example.com');
        $this->createUser($this->gymB, 'b@example.com');

        $this->assertSame(2, User::withoutTenantScope()->count());
        $this->assertSame(1, User::query()->forTenant($this->gymB->id)->count());
    }

    public function test_context_is_restored_after_nested_runs(): void
    {
        $this->context->run($this->gymA->id, function (): void {
            $this->context->run($this->gymB->id, fn () => $this->createMethod($this->gymB, PaymentMode::Cash, true));

            $this->assertSame($this->gymA->id, $this->context->id(), 'Le contexte précédent doit être restauré.');
        });
    }

    private function createUser(Gym $gym, string $email, UserRole $role = UserRole::Cashier): User
    {
        return $this->context->run($gym->id, fn () => User::factory()->create([
            'email' => $email,
            'role' => $role,
        ]));
    }

    private function createMethod(Gym $gym, PaymentMode $mode, bool $enabled): PaymentMethodSetting
    {
        return $this->context->run($gym->id, fn () => PaymentMethodSetting::create([
            'method' => $mode,
            'enabled' => $enabled,
        ]));
    }
}
