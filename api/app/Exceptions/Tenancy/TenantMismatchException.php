<?php

declare(strict_types=1);

namespace App\Exceptions\Tenancy;

use RuntimeException;

/**
 * Levée lorsqu'une écriture vise explicitement une autre salle que la salle courante.
 *
 * Traduit la règle du CDC §6.3 : aucune donnée d'un tenant ne peut être écrite depuis
 * le compte d'un autre tenant.
 */
final class TenantMismatchException extends RuntimeException
{
    public static function forModel(string $model, int $assignedTenantId, int $currentTenantId): self
    {
        return new self(sprintf(
            'Écriture refusée pour %s : la salle visée (%d) diffère de la salle courante (%d).',
            $model,
            $assignedTenantId,
            $currentTenantId
        ));
    }

    /**
     * Le rattachement d'un enregistrement à une salle est immuable : seule une
     * création peut le fixer, jamais une mise à jour.
     */
    public static function forUpdate(string $model, int $currentTenantId, int $targetTenantId): self
    {
        return new self(sprintf(
            'Modification refusée pour %s : le rattachement à la salle est immuable (%d → %d).',
            $model,
            $currentTenantId,
            $targetTenantId
        ));
    }
}
