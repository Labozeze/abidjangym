<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Utilisateur d'une salle : gérant (`admin`) ou caissier (`cashier`) — CDC §4.2.10.
 *
 * Multi-tenant : le trait `BelongsToTenant` filtre toute requête sur la salle courante et
 * refuse une écriture visant une autre salle.
 *
 * ⚠️ L'email est désormais **unique sur toute la plateforme** (décision du 01/11/2026) : la
 * connexion peut donc retrouver un compte à partir du seul email. La recherche doit malgré
 * tout se faire **hors** du scope multi-tenant, puisque le contexte n'existe pas encore à
 * ce moment-là :
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
    use HasApiTokens;

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

    /**
     * Email normalisé en minuscules : clé de connexion et de réinitialisation.
     *
     * @return Attribute<string, string>
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => mb_strtolower(trim($value)),
        );
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
