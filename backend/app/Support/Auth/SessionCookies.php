<?php

declare(strict_types=1);

namespace App\Support\Auth;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Cookies `httpOnly` du backoffice (CDC §6.2) : le jeton n'est jamais accessible au
 * JavaScript de la page.
 */
final class SessionCookies
{
    public static function access(string $token): Cookie
    {
        return self::make(SessionTokens::ACCESS_COOKIE, $token, SessionTokens::ACCESS_TTL_MINUTES);
    }

    public static function refresh(string $token, int $minutes): Cookie
    {
        return self::make(SessionTokens::REFRESH_COOKIE, $token, $minutes);
    }

    public static function forgetAccess(): Cookie
    {
        return self::make(SessionTokens::ACCESS_COOKIE, '', -1);
    }

    public static function forgetRefresh(): Cookie
    {
        return self::make(SessionTokens::REFRESH_COOKIE, '', -1);
    }

    private static function make(string $name, string $value, int $minutes): Cookie
    {
        $secure = app()->environment('production') || request()->isSecure();

        return cookie(
            name: $name,
            value: $value,
            minutes: $minutes,
            path: '/',
            domain: null,
            secure: $secure,
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }
}
