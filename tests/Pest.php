<?php

declare(strict_types=1);

/**
 * Pest tests: Pest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\User;
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
    $admin = User::factory()->admin()->create();
    test()->actingAs($admin);

    return $admin;
}
