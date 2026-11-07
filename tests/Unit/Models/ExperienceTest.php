<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / ExperienceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Experience;

it('formats a finished period', function (): void {
    $experience = Experience::factory()->make(['started_at' => '2020-03-01', 'ended_at' => '2023-07-31']);

    expect($experience->isCurrent())->toBeFalse()
        ->and($experience->period())->toBe('Mar 2020 — Jul 2023');
});

it('formats a current role as present', function (): void {
    $experience = Experience::factory()->current()->make(['started_at' => '2024-01-15']);

    expect($experience->isCurrent())->toBeTrue()
        ->and($experience->period())->toBe('Jan 2024 — Present');
});

it('lists current roles first, then most recent', function (): void {
    $old = Experience::factory()->create(['started_at' => '2015-01-01', 'ended_at' => '2018-01-01']);
    $recent = Experience::factory()->create(['started_at' => '2018-02-01', 'ended_at' => '2021-01-01']);
    $current = Experience::factory()->current()->create(['started_at' => '2021-02-01']);

    expect(Experience::query()->latestFirst()->pluck('id')->all())->toBe([$current->id, $recent->id, $old->id]);
});
