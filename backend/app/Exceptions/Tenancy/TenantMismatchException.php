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
}
