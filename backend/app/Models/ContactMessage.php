<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Message envoyé depuis la page contact du site public (CDC §4.1.2).
 *
 * Il n'appartient à aucune salle : c'est un message adressé à l'équipe AbidjanGym,
 * donc pas de `tenant_id` ni de scope multi-tenant.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $subject
 * @property string $message
 */
final class ContactMessage extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
    ];

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
