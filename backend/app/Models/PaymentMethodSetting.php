<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMode;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * Réglage d'un moyen de paiement pour une salle (CDC §4.2.6, activation on/off).
 *
 * Deux protections sur `credentials` (CDC §6.6) :
 *  - cast `encrypted:array` : la valeur est chiffrée avec l'`APP_KEY` avant d'atteindre la
 *    base, et déchiffrée à la lecture ;
 *  - `$hidden` : le champ n'apparaît **jamais** dans `toArray()`, `toJson()` ni dans une
 *    réponse d'API, même par accident.
 *
 * @property int $id
 * @property int $tenant_id
 * @property PaymentMode $method
 * @property bool $enabled
 * @property array<string, string>|null $credentials
 */
final class PaymentMethodSetting extends Model
{
    use BelongsToTenant;

    /** @var list<string> */
    protected $fillable = [
        'method',
        'enabled',
        'collection_number',
        'account_holder',
        'bank_name',
        'instructions',
        'credentials',
    ];

    /** @var list<string> */
    protected $hidden = [
        'credentials',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'method' => PaymentMode::class,
            'enabled' => 'boolean',
            'credentials' => 'encrypted:array',
        ];
    }

    /**
     * Traduit le champ `is_configured` du contrat : un moyen ne peut être activé que si sa
     * configuration minimale est renseignée (CDC §4.2.6).
     */
    public function isConfigured(): bool
    {
        return match ($this->method) {
            PaymentMode::Cash => true,
            PaymentMode::OrangeMoney, PaymentMode::MtnMomo, PaymentMode::Wave => filled($this->collection_number),
            PaymentMode::BankTransfer, PaymentMode::Cheque => filled($this->account_holder) && filled($this->bank_name),
            PaymentMode::CinetPay => true,
        };
    }
}
