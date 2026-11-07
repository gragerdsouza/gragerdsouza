<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / CertificationTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Certification;

it('describes a certification without an expiry date', function (): void {
    $certification = Certification::factory()->make(['issued_at' => '2025-02-01', 'expires_at' => null]);

    expect($certification->isExpired())->toBeFalse()
        ->and($certification->validity())->toBe('Issued Feb 2025');
});

it('describes an expired certification', function (): void {
    $certification = Certification::factory()->make(['issued_at' => '2019-10-01', 'expires_at' => '2022-10-01']);

    expect($certification->isExpired())->toBeTrue()
        ->and($certification->validity())->toBe('Issued Oct 2019 · Expired Oct 2022');
});

it('describes a certification that expires in the future', function (): void {
    $certification = Certification::factory()->make(['issued_at' => '2025-01-01', 'expires_at' => now()->addYears(2)->startOfMonth()]);

    expect($certification->isExpired())->toBeFalse()
        ->and($certification->validity())->toStartWith('Issued Jan 2025 · Expires ');
});

it('lists newest first, then alphabetically', function (): void {
    $old = Certification::factory()->create(['name' => 'Old', 'issued_at' => '2019-10-01']);
    $b = Certification::factory()->create(['name' => 'Beta', 'issued_at' => '2025-01-01']);
    $a = Certification::factory()->create(['name' => 'Alpha', 'issued_at' => '2025-01-01']);
    $newest = Certification::factory()->create(['name' => 'Zulu', 'issued_at' => '2025-02-01']);

    expect(Certification::query()->latestFirst()->pluck('id')->all())->toBe([$newest->id, $a->id, $b->id, $old->id]);
});

it('scopes to certifications that have not expired', function (): void {
    $noExpiry = Certification::factory()->create(['expires_at' => null]);
    $future = Certification::factory()->create(['expires_at' => now()->addYear()]);
    Certification::factory()->expired()->create();

    expect(Certification::query()->current()->pluck('id')->sort()->values()->all())->toBe([$noExpiry->id, $future->id]);
});
