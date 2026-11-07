<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / UserTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\MassAssignmentException;

it('only lets admins into the panel', function (): void {
    $panel = Filament::getPanel('admin');

    expect(User::factory()->admin()->make()->canAccessPanel($panel))->toBeTrue()
        ->and(User::factory()->make()->canAccessPanel($panel))->toBeFalse();
});

it('does not allow is_admin to be mass assigned', function (): void {
    new User(['name' => 'x', 'email' => 'x@example.com', 'password' => 'secret', 'is_admin' => true]);
})->throws(MassAssignmentException::class, 'is_admin');
