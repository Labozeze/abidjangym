<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Gym;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Données fictives : le dépôt est public (AGENTS.md §5.2).
 *
 * @extends Factory<Gym>
 */
final class GymFactory extends Factory
{
    /** @var class-string<Gym> */
    protected $model = Gym::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Salle '.fake()->unique()->city(),
            'address' => fake()->streetAddress(),
            'district' => 'Cocody',
            'city' => 'Abidjan',
            'phone' => '+22507000000'.fake()->numberBetween(10, 99),
            'whatsapp' => '+22507000000'.fake()->numberBetween(10, 99),
            'email' => fake()->unique()->safeEmail(),
            'timezone' => 'Africa/Abidjan',
            'currency' => 'XOF',
        ];
    }
}
