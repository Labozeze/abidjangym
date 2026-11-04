<?php

declare(strict_types=1);

namespace App\Exceptions\Tenancy;

use RuntimeException;

/**
 * Levée lorsqu'une écriture multi-tenant est tentée sans salle courante.
 *
 * C'est une erreur de programmation, pas une erreur utilisateur : le contexte doit
 * être posé par le middleware d'authentification ou par une commande explicite.
 */
final class MissingTenantContextException extends RuntimeException
{
    public static function forModel(string $model): self
    {
        return new self(sprintf(
            'Aucune salle courante n’est définie : impossible de créer un enregistrement %s.',
            $model
        ));
    }
}
