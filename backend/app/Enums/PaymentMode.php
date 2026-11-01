<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Moyens de paiement du CDC §4.2.6 (schéma `PaymentMode` du contrat).
 *
 * Chacun s'active ou se désactive par salle (`payment_method_settings`), et seuls les
 * moyens activés sont proposés à l'encaissement (CDC v2.2, ticket #23).
 */
enum PaymentMode: string
{
    case Cash = 'cash';
    case OrangeMoney = 'orange_money';
    case MtnMomo = 'mtn_momo';
    case Wave = 'wave';
    case CinetPay = 'cinetpay';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';

    /** Libellé en français, pour l'affichage dans le backoffice et sur les reçus. */
    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::OrangeMoney => 'Orange Money',
            self::MtnMomo => 'MTN MoMo',
            self::Wave => 'Wave',
            self::CinetPay => 'Carte bancaire (CinetPay)',
            self::BankTransfer => 'Virement',
            self::Cheque => 'Chèque',
        };
    }

    /**
     * Indique si le moyen repose sur un prestataire externe, donc sur des identifiants
     * secrets (chiffrés, écriture seule).
     */
    public function usesProviderCredentials(): bool
    {
        return match ($this) {
            self::Cash, self::BankTransfer, self::Cheque => false,
            self::OrangeMoney, self::MtnMomo, self::Wave, self::CinetPay => true,
        };
    }
}
