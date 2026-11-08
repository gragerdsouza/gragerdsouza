<?php

declare(strict_types=1);

/**
 * Pest tests: Pest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\User;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/**
 * Sign in as a Filament admin for the rest of the test.
 *
 * @return User The signed-in admin user.
 */
function actingAsAdmin(): User
{
    $admin = User::factory()->admin()->withAppAuthentication()->create();
    test()->actingAs($admin);

    return $admin;
}

/**
 * Evaluate a config file with temporary environment variables, then restore them.
 *
 * @param string $file Config file name, e.g. "session.php".
 * @param array<string, string|null> $variables Variables to set; null unsets one.
 *
 * @return array<string, mixed> The evaluated configuration array.
 */
function configWithEnv(string $file, array $variables): array
{
    // Mutable view over the same stores Laravel's env() reads: $_SERVER, $_ENV and getenv().
    $environment = RepositoryBuilder::createWithDefaultAdapters()->addAdapter(PutenvAdapter::class)->make();
    $previous = [];

    foreach ($variables as $name => $value) {
        $previous[$name] = $environment->get($name);
        $value === null ? $environment->clear($name) : $environment->set($name, $value);
    }

    try {
        return require config_path($file);
    } finally {
        foreach ($previous as $name => $value) {
            $value === null ? $environment->clear($name) : $environment->set($name, $value);
        }
    }
}
