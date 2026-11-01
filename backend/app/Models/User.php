<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Utilisateur d'une salle : gérant (`admin`) ou caissier (`cashier`) — CDC §4.2.10.
 *
 * Multi-tenant : le trait `BelongsToTenant` filtre toute requête sur la salle courante et
 * refuse une écriture visant une autre salle.
 *
 * ⚠️ Conséquence pour le ticket d'authentification : la connexion se fait par email + mot
 * de passe, or le même email peut exister dans plusieurs salles. La recherche du compte
 * devra donc se faire **hors** du scope, puis poser le `TenantContext` à partir du
 * `tenant_id` du compte trouvé :
 *
 *     $user = User::withoutTenantScope()->where('email', $email)->first();
 *     // … vérification du mot de passe, puis :
 *     app(TenantContext::class)->set($user->tenant_id);
 *
 * Ne jamais exposer `tenant_id` dans une réponse : le contrat expose `gym_id`.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property UserRole $role
 * @property bool $is_active
 */
final class User extends Authenticatable
{
    use BelongsToTenant;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * `tenant_id` est volontairement absent : il est renseigné par `BelongsToTenant` à
     * partir du contexte, jamais par assignation de masse (CDC §6.3).
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Gym, $this> */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class, 'tenant_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /** Nom complet, pour l'affichage (reçus, journal d'audit). */
    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
