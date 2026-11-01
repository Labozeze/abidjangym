<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * `POST /auth/forgot-password` et `POST /auth/reset-password` (CDC §4.2.1, §6.2) :
 * lien à usage unique valable 1 h, mot de passe de 8 caractères minimum avec majuscule et
 * chiffre.
 */
final class PasswordResetTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // La limitation de débit est testée séparément (RateLimitTest).
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_it_sends_a_reset_link_for_a_known_address(): void
    {
        Notification::fake();

        $user = $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com']);

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'gerant@example.com'])
            ->assertStatus(202)
            ->assertJsonPath('message', 'Si cette adresse existe, un lien de réinitialisation vous a été envoyé.');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_it_does_not_reveal_unknown_addresses(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
            ->assertStatus(202)
            ->assertJsonPath('message', 'Si cette adresse existe, un lien de réinitialisation vous a été envoyé.');

        Notification::assertNothingSent();
    }

    public function test_it_resets_the_password_with_a_valid_token(): void
    {
        $user = $this->makeAdmin($this->makeGym(), [
            'email' => 'gerant@example.com',
            'password' => 'AncienPass1',
        ]);

        $token = Password::broker()->createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'gerant@example.com',
            'token' => $token,
            'password' => 'NouveauPass1',
            'password_confirmation' => 'NouveauPass1',
        ])->assertNoContent();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'NouveauPass1',
        ])->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'AncienPass1',
        ])->assertUnauthorized();
    }

    public function test_it_revokes_existing_sessions_after_a_reset(): void
    {
        $user = $this->makeAdmin($this->makeGym(), [
            'email' => 'gerant@example.com',
            'password' => 'AncienPass1',
        ]);

        $access = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'AncienPass1',
        ])->json('access_token');

        $token = Password::broker()->createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'gerant@example.com',
            'token' => $token,
            'password' => 'NouveauPass1',
            'password_confirmation' => 'NouveauPass1',
        ])->assertNoContent();

        $this->withToken($access)->getJson('/api/v1/auth/me')->assertUnauthorized();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_weak_passwords_are_rejected(): void
    {
        $user = $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com']);
        $token = Password::broker()->createToken($user);

        $cases = [
            'Abcdef1' => 'trop court (7 caractères)',
            'abcdefgh1' => 'sans majuscule',
            'Abcdefgh' => 'sans chiffre',
        ];

        foreach ($cases as $password => $reason) {
            $this->postJson('/api/v1/auth/reset-password', [
                'email' => 'gerant@example.com',
                'token' => $token,
                'password' => $password,
                'password_confirmation' => $password,
            ])
                ->assertStatus(422)
                ->assertJsonValidationErrors(['password'], 'errors', $reason);
        }
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com']);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'gerant@example.com',
            'token' => 'jeton-invalide',
            'password' => 'NouveauPass1',
            'password_confirmation' => 'NouveauPass1',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_an_unknown_address_is_rejected_without_revealing_it(): void
    {
        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'inconnu@example.com',
            'token' => 'jeton-invalide',
            'password' => 'NouveauPass1',
            'password_confirmation' => 'NouveauPass1',
        ])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Ce lien de réinitialisation est invalide ou a expiré.');
    }
}
