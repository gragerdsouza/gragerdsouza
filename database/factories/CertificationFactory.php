<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for fake Certification models used by tests and seeders.
 *
 * @extends Factory<Certification>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class CertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'issuer' => fake()->company(),
            'issued_at' => fake()->dateTimeBetween('-3 years', '-1 month'),
            'expires_at' => null,
            'credential_url' => fake()->url(),
        ];
    }

    /**
     * State for a certification that has expired.
     *
     * @return static The factory with this state applied.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'issued_at' => now()->subYears(4),
            'expires_at' => now()->subYear(),
        ]);
    }
}
