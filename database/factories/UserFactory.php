<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory for fake User models used by tests and seeders.
 *
 * @extends Factory<User>
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> Default attribute values.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'app_authentication_secret' => null,
            'app_authentication_recovery_codes' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static The factory with this state applied.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return static The factory with this state applied.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_admin' => true,
        ]);
    }

    /**
     * Indicate that the user has set up authenticator-app two-factor authentication.
     *
     * @return static The factory with this state applied.
     */
    public function withAppAuthentication(): static
    {
        return $this->state(fn (array $attributes): array => [
            'app_authentication_secret' => AppAuthentication::make()->generateSecret(),
        ]);
    }
}
