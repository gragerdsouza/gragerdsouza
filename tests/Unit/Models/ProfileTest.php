<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / ProfileTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

it('returns the saved profile', function (): void {
    $first = Profile::factory()->create(['name' => 'First']);
    Profile::factory()->create(['name' => 'Second']);

    expect(Profile::current()->is($first))->toBeTrue();
});

it('falls back to an unsaved placeholder before setup', function (): void {
    config(['app.name' => 'My Portfolio', 'mail.from.address' => 'from@example.com']);

    $profile = Profile::current();

    expect($profile->exists)->toBeFalse()
        ->and($profile->name)->toBe('My Portfolio')
        ->and($profile->email)->toBe('from@example.com')
        ->and($profile->available_for_work)->toBeFalse()
        ->and($profile->social_links)->toBe([]);
});

it('builds the avatar url from the public disk', function (): void {
    Storage::fake('public');

    expect(Profile::factory()->make(['avatar_path' => null])->avatarUrl())->toBeNull()
        ->and(Profile::factory()->make(['avatar_path' => 'avatars/me.jpg'])->avatarUrl())->toEndWith('avatars/me.jpg');
});

it('casts its attributes', function (): void {
    $profile = Profile::factory()->create(['social_links' => ['github' => 'https://github.com/me'], 'available_for_work' => 1]);

    expect($profile->fresh()?->social_links)->toBe(['github' => 'https://github.com/me'])
        ->and($profile->fresh()?->available_for_work)->toBeTrue();
});
