<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * Limitation des tentatives par **limiteurs nommés**, un compteur par route (CDC §6.2, §6.4).
 *
 * La relecture de la PR #64 a montré que `throttle:5,1` partageait un compteur entre toutes
 * les routes : épuiser le formulaire de contact bloquait la connexion sur une IP partagée.
 */
final class RateLimitTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    public function test_login_is_blocked_after_five_attempts(): void
    {
        $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'gerant@example.com',
                'password' => 'MauvaisMotDePasse1',
            ])->assertUnauthorized();
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ]);

        $response->assertStatus(429)
            ->assertJsonPath('message', 'Trop de tentatives. Réessayez dans une minute.')
            ->assertJsonStructure(['message', 'errors']);

        $this->assertNotNull($response->headers->get('Retry-After'), 'La réponse 429 doit indiquer Retry-After.');
    }

    public function test_forgot_password_is_rate_limited_too(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
                ->assertStatus(202);
        }

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Trop de tentatives. Réessayez dans une minute.');
    }

    public function test_the_contact_limiter_does_not_block_login(): void
    {
        Mail::fake();

        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        // Épuise le limiteur du formulaire de contact…
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/contact-messages', [
                'first_name' => 'Visiteur',
                'last_name' => 'Test',
                'email' => 'visiteur@example.com',
                'subject' => 'Question',
                'message' => 'Bonjour, une question.',
            ])->assertCreated();
        }

        $this->postJson('/api/v1/contact-messages', [
            'first_name' => 'Visiteur',
            'last_name' => 'Test',
            'email' => 'visiteur@example.com',
            'subject' => 'Question',
            'message' => 'Bonjour, une question.',
        ])->assertStatus(429);

        // … la connexion doit rester disponible : c'était le défaut relevé en relecture.
        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->assertOk();
    }

    public function test_the_login_limiter_does_not_block_the_contact_form(): void
    {
        Mail::fake();

        $this->makeAdmin($this->makeGym(), ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'gerant@example.com',
                'password' => 'MauvaisMotDePasse1',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->assertStatus(429);

        $this->postJson('/api/v1/contact-messages', [
            'first_name' => 'Visiteur',
            'last_name' => 'Test',
            'email' => 'visiteur@example.com',
            'subject' => 'Question',
            'message' => 'Bonjour, une question.',
        ])->assertCreated();
    }
}
