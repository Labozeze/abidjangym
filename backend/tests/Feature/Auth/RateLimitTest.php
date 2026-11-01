<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * Limitation des tentatives : 5 par minute et par IP, avec `429` (CDC §6.2, §6.4).
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

        $response->assertStatus(429);
        $this->assertNotNull($response->headers->get('Retry-After'), 'La réponse 429 doit indiquer Retry-After.');
    }

    public function test_forgot_password_is_rate_limited_too(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
                ->assertStatus(202);
        }

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'inconnu@example.com'])
            ->assertStatus(429);
    }
}
