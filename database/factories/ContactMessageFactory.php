<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for fake ContactMessage models used by tests and seeders.
 *
 * @extends Factory<ContactMessage>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'ip_address' => fake()->ipv4(),
            'read_at' => null,
        ];
    }

    /**
     * State for a message that has been read.
     *
     * @return static The factory with this state applied.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes): array => ['read_at' => now()]);
    }
}
