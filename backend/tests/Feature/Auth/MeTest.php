<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * `GET /auth/me` : utilisateur, rôle et salle courants (contrat v0.3.0).
 */
final class MeTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    public function test_it_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_it_returns_the_user_role_and_gym(): void
    {
        $gym = $this->makeGym();
        $user = $this->makeAdmin($gym, ['email' => 'gerant@example.com']);
        $token = $user->createToken('access:test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'gerant@example.com')
            ->assertJsonPath('data.role', 'admin')
            ->assertJsonPath('data.gym_id', $gym->id)
            ->assertJsonPath('data.is_active', true)
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.tenant_id');
    }
}
