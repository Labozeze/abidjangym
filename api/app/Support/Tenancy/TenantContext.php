<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Closure;
use InvalidArgumentException;

/**
 * Porte la salle (tenant) courante pour la requête ou la commande en cours.
 *
 * Enregistré comme singleton dans le conteneur : un seul tenant est actif à la fois,
 * ce qui rend impossible une fuite de données entre deux salles (CDC §6.3).
 *
 * Aucune valeur par défaut : tant que rien n'est défini, les modèles multi-tenant ne
 * renvoient aucune ligne (refus par défaut, voir TenantScope).
 */
final class TenantContext
{
    private ?int $tenantId = null;

    /** Identifiant de la salle courante, ou null si aucun contexte n'est établi. */
    public function id(): ?int
    {
        return $this->tenantId;
    }

    public function has(): bool
    {
        return $this->tenantId !== null;
    }

    public function set(?int $tenantId): void
    {
        if ($tenantId !== null && $tenantId < 1) {
            throw new InvalidArgumentException('L’identifiant de salle doit être un entier positif.');
        }

        $this->tenantId = $tenantId;
    }

    public function forget(): void
    {
        $this->tenantId = null;
    }

    /**
     * Exécute un traitement dans le contexte d'une salle, puis restaure le contexte
     * précédent — y compris si le traitement lève une exception.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public function run(int $tenantId, Closure $callback): mixed
    {
        $previous = $this->tenantId;

        $this->set($tenantId);

        try {
            return $callback();
        } finally {
            $this->tenantId = $previous;
        }
    }
}
