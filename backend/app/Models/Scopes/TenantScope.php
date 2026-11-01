<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtre automatiquement toute requête sur la salle courante (CDC §6.3).
 *
 * Comportement volontairement « fail closed » : sans tenant courant, la requête ne
 * renvoie aucune ligne plutôt que toutes les lignes. Une commande de maintenance qui
 * doit réellement voir toutes les salles le fait explicitement avec
 * `Model::withoutTenantScope()` — la dérogation est ainsi visible dans le code.
 */
final class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = app(TenantContext::class)->id();

        if ($tenantId === null) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
    }
}
