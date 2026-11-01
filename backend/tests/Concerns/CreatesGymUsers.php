<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Enums\UserRole;
use App\Models\Gym;
use App\Models\User;
use App\Support\Tenancy\TenantContext;

/**
 * Fabrique une salle et ses utilisateurs en respectant le contexte multi-tenant.
 */
trait CreatesGymUsers
{
    protected function makeGym(string $name = 'FitZone Test'): Gym
    {
        return Gym::factory()->create(['name' => $name]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeUser(Gym $gym, array $attributes = []): User
    {
        return app(TenantContext::class)->run(
            $gym->id,
            fn (): User => User::factory()->create($attributes)
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeAdmin(Gym $gym, array $attributes = []): User
    {
        return $this->makeUser($gym, [...$attributes, 'role' => UserRole::Admin]);
    }
}
