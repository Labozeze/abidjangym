<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bannière d'une salle : trois maximum, affichées dans l'espace abonné (CDC §4.2.3).
 */
final class GymBanner extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'gym_id',
        'url',
        'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Gym, $this> */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
