<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Support\Auth\SessionTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * `POST /auth/login` (contrat v0.3.0, CDC §4.2.1 et §6.2).
 */
final class LoginTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    public function test_it_returns_tokens_and_sets_http_only_cookies(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('expires_in', 3600)
            ->assertJsonPath('user.email', 'gerant@example.com')
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonPath('user.gym_id', $gym->id)
            ->assertJsonStructure(['user', 'token_type', 'access_token', 'refresh_token', 'expires_in']);

        $this->assertNotEmpty($response->json('access_token'));

        $accessCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === SessionTokens::ACCESS_COOKIE);
        $refreshCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === SessionTokens::REFRESH_COOKIE);

        $this->assertNotNull($accessCookie, 'Le cookie de session doit être posé.');
        $this->assertTrue($accessCookie->isHttpOnly(), 'Le cookie doit être httpOnly (CDC §6.2).');
        $this->assertNotNull($refreshCookie);
        $this->assertTrue($refreshCookie->isHttpOnly());
    }

    public function test_the_access_token_authenticates_subsequent_requests(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->json('access_token');

        $this->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'gerant@example.com');
    }

    public function test_the_session_cookie_authenticates_subsequent_requests(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ]);

        $access = $response->json('access_token');

        // `withCredentials()` est indispensable : sans lui, le client de test n'envoie
        // aucun cookie. Valeur brute, comme un navigateur (pas d'EncryptCookies sur l'API).
        $this->withCredentials()
            ->withUnencryptedCookie(SessionTokens::ACCESS_COOKIE, $access)
            ->getJson('/api/v1/auth/me')
            ->assertOk();
    }

    public function test_an_unknown_email_is_rejected(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'inconnu@example.com',
            'password' => 'MotDePasse1',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Identifiants incorrects.');
    }

    public function test_a_wrong_password_is_rejected(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MauvaisMotDePasse1',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Identifiants incorrects.');
    }

    public function test_a_disabled_user_cannot_log_in(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Ce compte est désactivé. Contactez le gérant de votre salle.');
    }

    public function test_the_email_is_case_insensitive(): void
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'Gerant@Example.COM', 'password' => 'MotDePasse1']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'GERANT@example.com',
            'password' => 'MotDePasse1',
        ])->assertOk();
    }

    public function test_missing_fields_are_validation_errors(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_the_last_login_timestamp_is_recorded(): void
    {
        $gym = $this->makeGym();
        $user = $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        $this->assertNull($user->last_login_at);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->assertOk();

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_the_password_is_hashed_with_the_configured_bcrypt_cost(): void
    {
        $gym = $this->makeGym();
        $user = $this->makeAdmin($gym, ['password' => 'MotDePasse1']);

        $info = password_get_info($user->password);

        $this->assertSame('bcrypt', $info['algoName']);
        $this->assertSame(
            (int) config('hashing.bcrypt.rounds'),
            $info['options']['cost'],
            'Le coût bcrypt doit suivre la configuration (12 en production, 4 en test).'
        );
        $this->assertTrue(Hash::check('MotDePasse1', $user->password));
    }
}
