<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Point d'entrée de `php artisan db:seed`.
 *
 * ⚠️ Le trait `WithoutModelEvents` de Laravel est volontairement **absent** : le socle
 * multi-tenant repose sur les événements de modèle (`creating`/`updating` de
 * `BelongsToTenant`). Les désactiver produirait des lignes sans `tenant_id`.
 */
final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoDataSeeder::class);
    }
}
