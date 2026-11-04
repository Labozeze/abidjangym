<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Gym;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le seeder de démonstration utilise un mot de passe non conforme au CDC §6.2 : il ne doit
 * jamais s'exécuter hors local/testing (relecture de la PR #62).
 */
final class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_two_gyms_with_their_users_in_local(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(2, Gym::query()->count());
        $this->assertSame(4, User::withoutTenantScope()->count());
    }

    public function test_it_does_nothing_outside_local_and_testing(): void
    {
        $this->app['env'] = 'production';

        // Appel direct du seeder : `php artisan db:seed` demanderait une confirmation
        // interactive en production (ConfirmableTrait), hors du sujet de ce test.
        (new DemoDataSeeder)->setContainer($this->app)->run();

        $this->assertSame(0, Gym::query()->count(), 'Aucune donnée de démonstration ne doit être créée.');
        $this->assertSame(0, User::withoutTenantScope()->count());
    }
}
