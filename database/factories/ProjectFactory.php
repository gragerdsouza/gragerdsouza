<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory for fake Project models used by tests and seeders.
 *
 * @extends Factory<Project>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->unique()->sentence(3),
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(12),
            'body' => "## Overview\n\n".fake()->paragraph(),
            'tech_stack' => ['Laravel', 'PHP', 'Tailwind CSS'],
            'url' => fake()->url(),
            'repository_url' => 'https://github.com/example/'.Str::slug($title),
            'is_featured' => false,
            'published_at' => now()->subDay(),
            'sort_order' => 0,
        ];
    }

    /**
     * State for a featured project.
     *
     * @return static The factory with this state applied.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => ['is_featured' => true]);
    }

    /**
     * State for an unpublished draft.
     *
     * @return static The factory with this state applied.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => null]);
    }

    /**
     * State for a project scheduled in the future.
     *
     * @return static The factory with this state applied.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => now()->addWeek()]);
    }
}
