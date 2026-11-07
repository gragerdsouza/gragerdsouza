<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for fake Profile models used by tests and seeders.
 *
 * @extends Factory<Profile>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class ProfileFactory extends Factory
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
            'headline' => 'Full-stack developer',
            'tagline' => fake()->sentence(),
            'bio' => fake()->paragraphs(2, true),
            'location' => fake()->city(),
            'email' => fake()->safeEmail(),
            'avatar_path' => null,
            'resume_url' => null,
            'social_links' => ['github' => 'https://github.com/example'],
            'available_for_work' => true,
        ];
    }
}
