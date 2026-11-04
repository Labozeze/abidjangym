<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * L'envoi des emails d'authentification doit être **différé** (`defer()`) : sinon le temps
 * de réponse révèle quels comptes existent (relecture de la PR #62, Benjamin).
 *
 * Le test remplace la collection de callbacks différés par un espion : il vérifie que le
 * callback est enregistré pendant la requête (et non exécuté), puis l'exécute lui-même.
 * C'est déterministe, contrairement à une mesure de latence.
 */
final class ForgotPasswordDeferralTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_the_reset_email_is_deferred_until_after_the_response(): void
    {
        Notification::fake();

        $user = $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com']);

        $spy = $this->swapDeferredCallbacks();

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'gerant@example.com'])
            ->assertStatus(202);

        $this->assertNotEmpty(
            $spy->queued,
            'L’envoi doit être différé : aucun callback ne doit s’exécuter pendant la requête.'
        );
        Notification::assertNothingSent();

        // Exécution manuelle de ce que la requête a mis de côté.
        foreach ($spy->queued as $callback) {
            $callback();
        }

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_an_unknown_address_registers_no_deferred_email(): void
    {
        $spy = $this->swapDeferredCallbacks();

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
            ->assertStatus(202);

        $this->assertSame([], $spy->queued, 'Aucun email ne doit être préparé pour une adresse inconnue.');
    }

    private function swapDeferredCallbacks(): DeferredCallbackCollection
    {
        $spy = new class extends DeferredCallbackCollection
        {
            /** @var list<callable> */
            public array $queued = [];

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->queued[] = $value;
            }

            public function invoke(): void
            {
                // Volontairement inerte : le test décide quand exécuter les callbacks.
            }
        };

        $this->swap(DeferredCallbackCollection::class, $spy);

        return $spy;
    }
}
