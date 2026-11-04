<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Rôles d'une salle (CDC §4.2.10, schéma `UserRole` du contrat).
 *
 * Le rôle plateforme « Super Admin » est hors périmètre de la v0.3 : il relèvera d'un
 * back-office distinct, non accessible depuis l'API d'une salle.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Cashier = 'cashier';

    /** Libellé en français, pour l'affichage dans le backoffice. */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Gérant',
            self::Cashier => 'Caissier',
        };
    }
}
