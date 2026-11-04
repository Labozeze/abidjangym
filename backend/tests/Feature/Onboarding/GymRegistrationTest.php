<?php

declare(strict_types=1);

namespace Tests\Feature\Onboarding;

use App\Enums\PaymentMode;
use App\Mail\GymRegisteredMail;
use App\Models\Gym;
use App\Models\PaymentMethodSetting;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

/**
 * `POST /gyms` — inscription d'une salle (CDC §10.1, contrat v0.3.0).
 */
final class GymRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_the_gym_its_admin_and_the_plan(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/gyms', $this->payload());

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['gym_id', 'administration_url', 'message']])
            ->assertJsonPath(
                'data.message',
                'Votre salle a été créée. Consultez votre email pour confirmer votre compte.'
            );

        $gym = Gym::query()->firstOrFail();

        $this->assertSame('FitZone Test', $gym->name);
        $this->assertSame('essentiel', $gym->plan_code);
        $this->assertSame('Cocody', $gym->district);
        $this->assertSame($gym->id, $response->json('data.gym_id'));
        $this->assertSame(
            rtrim((string) config('backoffice.url'), '/').'/dashboard',
            $response->json('data.administration_url')
        );

        Mail::assertSent(
            GymRegisteredMail::class,
            fn (GymRegisteredMail $mail): bool => $mail->hasTo('gerant@example.com')
        );
    }

    public function test_the_new_gym_has_seven_payment_methods_and_only_cash_is_enabled(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/gyms', $this->payload())->assertCreated();

        $settings = PaymentMethodSetting::withoutTenantScope()->get();

        $this->assertCount(7, $settings);
        $this->assertSame(
            [PaymentMode::Cash],
            $settings->where('enabled', true)->map(fn (PaymentMethodSetting $s): PaymentMode => $s->method)->values()->all(),
            'Seules les espèces doivent être activées à l’inscription (décision du 01/11/2026).'
        );
    }

    public function test_the_admin_can_log_in_after_onboarding(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/gyms', $this->payload())->assertCreated();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])
            ->assertOk()
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonPath('user.gym_id', Gym::query()->firstOrFail()->id);
    }

    public function test_it_refuses_an_email_already_used_in_another_gym_without_revealing_it(): void
    {
        $otherGym = Gym::factory()->create(['name' => 'Salle Témoin']);

        app(TenantContext::class)->run(
            $otherGym->id,
            fn () => User::factory()->create(['email' => 'gerant@example.com'])
        );

        $response = $this->postJson('/api/v1/gyms', $this->payload());

        $response->assertStatus(422)->assertJsonValidationErrors(['admin.email']);

        /** @var array<string, list<string>> $errors */
        $errors = $response->json('errors');

        $this->assertSame('Cette adresse email est déjà utilisée.', $errors['admin.email'][0]);
        $this->assertStringNotContainsString(
            'Salle Témoin',
            (string) json_encode($response->json(), JSON_UNESCAPED_UNICODE),
            'Le message ne doit jamais révéler à quelle salle appartient l’adresse.'
        );
        $this->assertSame(1, Gym::query()->count());
    }

    public function test_it_validates_the_payload(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $invalidPayloads = [
            'nom de salle manquant' => ['gym' => ['name' => '']],
            'téléphone non international' => ['gym' => ['phone' => '0700000001']],
            'mot de passe trop faible' => ['admin' => ['password' => 'faible']],
            'formule inconnue' => ['plan_code' => 'gold'],
            'CGU non acceptées' => ['terms_accepted' => false],
        ];

        foreach ($invalidPayloads as $case => $overrides) {
            $this->postJson('/api/v1/gyms', $this->payload($overrides))
                ->assertStatus(422, sprintf('Cas attendu en 422 : %s', $case));
        }

        $this->assertSame(0, Gym::query()->count(), 'Aucune salle ne doit être créée par une requête invalide.');
    }

    public function test_it_rolls_back_everything_when_a_step_fails(): void
    {
        Mail::fake();

        Event::listen('eloquent.creating: '.PaymentMethodSetting::class, function (): void {
            throw new RuntimeException('échec simulé au milieu de la transaction');
        });

        $this->withoutExceptionHandling();

        try {
            $this->postJson('/api/v1/gyms', $this->payload());
            $this->fail('Une exception était attendue.');
        } catch (RuntimeException $exception) {
            $this->assertSame('échec simulé au milieu de la transaction', $exception->getMessage());
        }

        $this->assertSame(0, Gym::query()->count(), 'La salle ne doit pas subsister.');
        $this->assertSame(0, User::withoutTenantScope()->count(), 'Le gérant ne doit pas subsister.');
        $this->assertSame(0, PaymentMethodSetting::withoutTenantScope()->count(), 'Les réglages ne doivent pas subsister.');
    }

    public function test_it_is_rate_limited(): void
    {
        Mail::fake();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/gyms', ['gym' => []])->assertStatus(422);
        }

        $this->postJson('/api/v1/gyms', $this->payload())->assertStatus(429);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'gym' => [
                'name' => 'FitZone Test',
                'district' => 'Cocody',
                'address' => 'Rue des Jardins, Cocody',
                'phone' => '+2250700000001',
                'whatsapp' => '+2250700000002',
            ],
            'admin' => [
                'first_name' => 'Awa',
                'last_name' => 'Démo',
                'email' => 'gerant@example.com',
                'mobile_money_number' => '+2250700000003',
                'password' => 'MotDePasse1',
            ],
            'plan_code' => 'essentiel',
            'terms_accepted' => true,
        ], $overrides);
    }
}
