<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Exceptions\Tenancy\MissingTenantContextException;
use App\Exceptions\Tenancy\TenantMismatchException;
use App\Models\Scopes\TenantScope;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * À utiliser sur tout modèle dont la table porte une colonne `tenant_id`.
 *
 * Le trait apporte trois garanties (CDC §6.3) :
 *  - lecture : un scope global filtre sur la salle courante, et sans salle courante
 *    aucune ligne n'est visible ;
 *  - écriture : `tenant_id` est renseigné automatiquement à la création ;
 *  - garde-fou : impossible de créer une ligne pour une autre salle que la salle courante.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);
            $assigned = $model->getAttribute('tenant_id');

            if ($assigned === null) {
                if (! $context->has()) {
                    throw MissingTenantContextException::forModel($model::class);
                }

                $model->setAttribute('tenant_id', $context->id());

                return;
            }

            if ($context->has() && (int) $assigned !== $context->id()) {
                throw TenantMismatchException::forModel($model::class, (int) $assigned, $context->id());
            }
        });
    }

    public function tenantId(): ?int
    {
        $value = $this->getAttribute('tenant_id');

        return $value === null ? null : (int) $value;
    }

    /**
     * Restreint explicitement la requête à une salle donnée, sans dépendre du contexte.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query
            ->withoutGlobalScope(TenantScope::class)
            ->where($this->qualifyColumn('tenant_id'), $tenantId);
    }

    /**
     * Dérogation explicite au scope, réservée aux commandes de maintenance et aux
     * exports d'administration. À ne jamais utiliser dans le chemin d'une requête HTTP.
     *
     * @return Builder<static>
     */
    public static function withoutTenantScope(): Builder
    {
        return static::query()->withoutGlobalScope(TenantScope::class);
    }
}
