<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for fake Experience models used by tests and seeders.
 *
 * @extends Factory<Experience>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'role' => fake()->jobTitle(),
            'location' => fake()->city(),
            'started_at' => fake()->dateTimeBetween('-8 years', '-2 years'),
            'ended_at' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'summary' => fake()->paragraph(),
        ];
    }

    /**
     * State for an ongoing role (no end date).
     *
     * @return static The saved profile or a placeholder.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes): array => ['ended_at' => null]);
    }
}
