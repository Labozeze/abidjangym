<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Support\Auth\SessionTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * `POST /auth/logout` : révocation serveur du jeton et effacement des cookies (CDC §6.2).
 */
final class LogoutTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    public function test_it_revokes_the_session_and_expires_the_cookies(): void
    {
        $gym = $this->makeGym();
        $user = $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $body = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->json();

        $response = $this->withToken($body['access_token'])
            ->postJson('/api/v1/auth/logout');

        $response->assertNoContent();
        $response->assertCookieExpired(SessionTokens::ACCESS_COOKIE);
        $response->assertCookieExpired(SessionTokens::REFRESH_COOKIE);

        $this->assertSame(0, $user->tokens()->count(), 'Tous les jetons de la session doivent être révoqués.');

        // Les tests réutilisent la même instance d'application : le guard Sanctum garde
        // l'utilisateur résolu de la requête précédente. On le vide pour simuler une
        // nouvelle requête HTTP.
        $this->app['auth']->forgetGuards();

        $this->withToken($body['access_token'])
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_the_refresh_token_of_the_session_is_also_revoked(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $body = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->json();

        $this->withToken($body['access_token'])->postJson('/api/v1/auth/logout')->assertNoContent();

        $this->postJson('/api/v1/auth/refresh', ['refresh_token' => $body['refresh_token']])
            ->assertUnauthorized();
    }

    public function test_it_requires_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
    }
}
