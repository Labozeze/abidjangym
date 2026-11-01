<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\Auth\SessionTokens;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesGymUsers;
use Tests\TestCase;

/**
 * `POST /auth/refresh` : jeton d'accès 1 h, rafraîchissement tant qu'il y a de l'activité,
 * session plafonnée à 8 h, ou 30 jours avec « se souvenir de cet appareil ».
 */
final class RefreshTest extends TestCase
{
    use CreatesGymUsers;
    use RefreshDatabase;

    public function test_it_rotates_the_refresh_token(): void
    {
        $body = $this->login();

        $refreshed = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $body['refresh_token'],
        ])
            ->assertOk()
            ->assertJsonStructure(['token_type', 'access_token', 'refresh_token', 'expires_in'])
            ->json();

        $this->assertNotSame($body['access_token'], $refreshed['access_token']);
        $this->assertNotSame($body['refresh_token'], $refreshed['refresh_token']);

        // L'ancien jeton de rafraîchissement a été consommé (rotation).
        $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $body['refresh_token'],
        ])->assertUnauthorized();
    }

    public function test_it_accepts_the_refresh_cookie(): void
    {
        $body = $this->login();

        $this->withCredentials()
            ->withUnencryptedCookie(SessionTokens::REFRESH_COOKIE, $body['refresh_token'])
            ->postJson('/api/v1/auth/refresh')
            ->assertOk();
    }

    public function test_an_invalid_refresh_token_is_rejected(): void
    {
        $this->postJson('/api/v1/auth/refresh', ['refresh_token' => 'jeton-inexistant'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Jeton de rafraîchissement invalide ou expiré.');
    }

    public function test_a_missing_refresh_token_is_rejected(): void
    {
        $this->postJson('/api/v1/auth/refresh')->assertUnauthorized();
    }

    public function test_the_session_is_capped_at_eight_hours(): void
    {
        $user = $this->userWithEmail();

        $token = $user->createToken(
            'refresh:test',
            [
                'session:test',
                'refresh',
                'started:'.CarbonImmutable::now()->subHours(9)->toIso8601String(),
            ],
            CarbonImmutable::now()->addHour(),
        );

        $this->postJson('/api/v1/auth/refresh', ['refresh_token' => $token->plainTextToken])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Votre session a expiré. Reconnectez-vous.');
    }

    public function test_remember_device_extends_the_cap_to_thirty_days(): void
    {
        $user = $this->userWithEmail();

        $token = $user->createToken(
            'refresh:test',
            [
                'session:test',
                'refresh',
                'started:'.CarbonImmutable::now()->subHours(9)->toIso8601String(),
                'remember',
            ],
            CarbonImmutable::now()->addDays(29),
        );

        $this->postJson('/api/v1/auth/refresh', ['refresh_token' => $token->plainTextToken])
            ->assertOk();
    }

    public function test_an_expired_access_token_is_rejected(): void
    {
        $user = $this->userWithEmail();

        $expired = $user->createToken('access:test', ['session:test'], CarbonImmutable::now()->subMinute());

        $this->withToken($expired->plainTextToken)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    /**
     * @return array<string, mixed>
     */
    private function login(): array
    {
        $gym = $this->makeGym();
        $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);

        return $this->postJson('/api/v1/auth/login', [
            'email' => 'gerant@example.com',
            'password' => 'MotDePasse1',
        ])->assertOk()->json();
    }

    private function userWithEmail(): User
    {
        $gym = $this->makeGym();

        return $this->makeAdmin($gym, ['email' => 'gerant@example.com', 'password' => 'MotDePasse1']);
    }
}
