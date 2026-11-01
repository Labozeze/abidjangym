<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle d'exemple servant à démontrer et à tester l'isolation multi-tenant.
 *
 * Il ne représente aucune entité métier : il matérialise le socle `tenant_id` +
 * scope global en attendant les vrais modèles (Gym, Member, Subscription, Payment).
 * À supprimer dès que le premier modèle métier multi-tenant est livré.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $name
 */
final class ExampleRecord extends Model
{
    use BelongsToTenant;

    protected $table = 'example_records';

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'name',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
        ];
    }
}
