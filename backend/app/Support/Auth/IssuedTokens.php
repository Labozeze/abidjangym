<?php

declare(strict_types=1);

namespace App\Support\Auth;

use Carbon\CarbonImmutable;

/**
 * Jetons émis à la connexion ou au rafraîchissement.
 *
 * `startedAt` matérialise le début de session : le plafond de 8 h (ou de 30 jours avec
 * « se souvenir de cet appareil ») se compte à partir de là, pas de la dernière émission.
 */
final readonly class IssuedTokens
{
    public function __construct(
        public string $sessionId,
        public string $accessToken,
        public string $refreshToken,
        public CarbonImmutable $startedAt,
        public int $accessExpiresIn,
        public int $refreshLifetimeMinutes,
    ) {}
}
