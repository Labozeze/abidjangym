<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GymFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * La salle de sport — c'est-à-dire **le tenant** (CDC §6.3).
 *
 * Ce modèle n'utilise volontairement pas `BelongsToTenant` : sa clé primaire *est* le
 * `tenant_id` des autres tables. Il n'est donc pas filtré par le contexte courant ; les
 * écrans d'administration d'une salle l'atteignent par l'identifiant porté par le jeton
 * de l'utilisateur authentifié.
 *
 * Champs alignés sur le schéma `Gym` du contrat (CDC §4.2.3, §4.2.8).
 *
 * @property int $id
 * @property string $name
 * @property string $timezone
 * @property string $currency
 */
final class Gym extends Model
{
    /** @use HasFactory<GymFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'logo_url',
        'tagline',
        'address',
        'district',
        'city',
        'phone',
        'whatsapp',
        'email',
        'website',
        'opening_hours',
        'primary_color',
        'secondary_color',
        'social_links',
        'timezone',
        'currency',
        'welcome_message',
        'receipt_footer_message',
        'receipt_include_qr',
        'receipt_signature_url',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'social_links' => 'array',
            'receipt_include_qr' => 'boolean',
        ];
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    /** @return HasMany<GymBanner, $this> */
    public function banners(): HasMany
    {
        return $this->hasMany(GymBanner::class)->orderBy('sort_order');
    }

    /** @return HasMany<PaymentMethodSetting, $this> */
    public function paymentMethodSettings(): HasMany
    {
        return $this->hasMany(PaymentMethodSetting::class, 'tenant_id');
    }
}
