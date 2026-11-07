<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SkillCategory;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for fake Skill models used by tests and seeders.
 *
 * @extends Factory<Skill>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'category' => fake()->randomElement(SkillCategory::cases()),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
