<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use RuntimeException;

/**
 * Échec d'authentification côté API : toujours traduit en `401` avec un message en
 * français, jamais avec un détail qui permettrait d'énumérer les comptes.
 */
final class AuthenticationFailedException extends RuntimeException
{
    public static function invalidCredentials(): self
    {
        return new self('Identifiants incorrects.');
    }

    public static function inactiveAccount(): self
    {
        return new self('Ce compte est désactivé. Contactez le gérant de votre salle.');
    }

    public static function invalidRefreshToken(): self
    {
        return new self('Jeton de rafraîchissement invalide ou expiré.');
    }

    public static function sessionExpired(): self
    {
        return new self('Votre session a expiré. Reconnectez-vous.');
    }
}
