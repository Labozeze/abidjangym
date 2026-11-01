<?php

declare(strict_types=1);

namespace App\Support\Auth;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Émission, rafraîchissement et révocation des jetons Sanctum (CDC §6.2).
 *
 * Politique décidée par Rufin (PR #20) :
 *  - jeton d'accès **1 h** ;
 *  - session **plafonnée à 8 h**, prolongée par rafraîchissement tant qu'il y a de
 *    l'activité ;
 *  - option « se souvenir de cet appareil » : plafond porté à **30 jours**.
 *
 * Les deux jetons portent l'identifiant de session et l'instant de début dans leurs
 * *abilities* : le plafond se vérifie donc sans table supplémentaire, et la déconnexion
 * peut révoquer la session entière.
 */
final class SessionTokens
{
    public const ACCESS_COOKIE = 'ag_session';

    public const REFRESH_COOKIE = 'ag_refresh';

    public const ACCESS_TTL_MINUTES = 60;

    public const SESSION_MAX_MINUTES = 480;

    public const REMEMBER_MAX_MINUTES = 43200;

    public function issue(User $user, bool $remember): IssuedTokens
    {
        return $this->issueForSession($user, (string) Str::uuid(), CarbonImmutable::now(), $remember);
    }

    /**
     * @throws AuthenticationFailedException
     */
    public function refresh(User $user, PersonalAccessToken $refreshToken): IssuedTokens
    {
        $abilities = $refreshToken->abilities ?? [];

        if (! in_array('refresh', $abilities, true)) {
            throw AuthenticationFailedException::invalidRefreshToken();
        }

        if ($refreshToken->expires_at !== null && $refreshToken->expires_at->isPast()) {
            throw AuthenticationFailedException::invalidRefreshToken();
        }

        $remember = in_array('remember', $abilities, true);
        $startedAt = $this->startedAtOf($abilities);
        $deadline = $startedAt->addMinutes($remember ? self::REMEMBER_MAX_MINUTES : self::SESSION_MAX_MINUTES);

        if (CarbonImmutable::now()->greaterThan($deadline)) {
            throw AuthenticationFailedException::sessionExpired();
        }

        // Rotation : l'ancien jeton de rafraîchissement ne sert qu'une fois.
        $refreshToken->delete();

        return $this->issueForSession($user, $this->sessionIdOf($abilities) ?? (string) Str::uuid(), $startedAt, $remember);
    }

    /** Révoque tous les jetons d'une session (déconnexion). */
    public function revokeSession(User $user, string $sessionId): void
    {
        $user->tokens()
            ->where('abilities', 'like', '%"session:'.$sessionId.'"%')
            ->delete();
    }

    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
    }

    /** @param  list<string>|null  $abilities */
    public function sessionIdOf(?array $abilities): ?string
    {
        foreach ($abilities ?? [] as $ability) {
            if (str_starts_with($ability, 'session:')) {
                return substr($ability, strlen('session:'));
            }
        }

        return null;
    }

    private function issueForSession(
        User $user,
        string $sessionId,
        CarbonImmutable $startedAt,
        bool $remember,
    ): IssuedTokens {
        $sessionAbility = 'session:'.$sessionId;
        $startedAbility = 'started:'.$startedAt->toIso8601String();

        $access = $user->createToken(
            'access:'.$sessionId,
            [$sessionAbility],
            CarbonImmutable::now()->addMinutes(self::ACCESS_TTL_MINUTES),
        );

        $refreshLifetime = $remember ? self::REMEMBER_MAX_MINUTES : self::SESSION_MAX_MINUTES;
        $refreshAbilities = [$sessionAbility, 'refresh', $startedAbility];

        if ($remember) {
            $refreshAbilities[] = 'remember';
        }

        $refresh = $user->createToken(
            'refresh:'.$sessionId,
            $refreshAbilities,
            $startedAt->addMinutes($refreshLifetime),
        );

        return new IssuedTokens(
            sessionId: $sessionId,
            accessToken: $access->plainTextToken,
            refreshToken: $refresh->plainTextToken,
            startedAt: $startedAt,
            accessExpiresIn: self::ACCESS_TTL_MINUTES * 60,
            refreshLifetimeMinutes: $refreshLifetime,
        );
    }

    /** @param  list<string>  $abilities */
    private function startedAtOf(array $abilities): CarbonImmutable
    {
        foreach ($abilities as $ability) {
            if (str_starts_with($ability, 'started:')) {
                return CarbonImmutable::parse(substr($ability, strlen('started:')));
            }
        }

        // Jeton émis hors du service (outil, test) : la session démarre maintenant.
        return CarbonImmutable::now();
    }
}
