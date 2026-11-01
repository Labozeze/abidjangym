<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Modèle de jeton Sanctum adapté au multi-tenant.
 *
 * ⚠️ Pourquoi ce modèle existe : Sanctum retrouve l'utilisateur via la relation
 * `tokenable` (`User::find(...)`). Or `User` porte le scope global de `BelongsToTenant` :
 * au moment de l'authentification, aucun `TenantContext` n'est encore posé (c'est
 * justement le jeton qui va le déterminer), donc la requête ne renverrait aucune ligne et
 * **toute authentification par jeton échouerait**.
 *
 * La relation lève donc explicitement le scope. Ce n'est pas une faille : le jeton
 * appartient à un utilisateur précis (`tokenable_id`), il ne permet aucune lecture métier —
 * le `TenantContext` est posé ensuite, par le middleware `tenant`.
 *
 * Elle est déclarée en `BelongsTo` (et non en `MorphTo`) parce que seul `User` porte des
 * jetons ; cela permet de construire la requête tout de suite et donc d'y appliquer
 * `withoutGlobalScopes()`.
 */
final class SanctumToken extends PersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    /** @return BelongsTo<User, $this> */
    public function tokenable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tokenable_id')
            ->withoutGlobalScopes();
    }
}
