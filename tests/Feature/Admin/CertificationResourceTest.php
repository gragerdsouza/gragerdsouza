<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / CertificationResourceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Filament\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Resources\Certifications\Pages\EditCertification;
use App\Filament\Resources\Certifications\Pages\ListCertifications;
use App\Models\Certification;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(fn (): User => actingAsAdmin());

it('lists certifications and filters out expired ones', function (): void {
    $current = Certification::factory()->create();
    $expired = Certification::factory()->expired()->create();

    Livewire::test(ListCertifications::class)
        ->assertCanSeeTableRecords([$current, $expired])
        ->filterTable('expired', false)
        ->assertCanSeeTableRecords([$current])
        ->assertCanNotSeeTableRecords([$expired])
        ->filterTable('expired', true)
        ->assertCanSeeTableRecords([$expired])
        ->assertCanNotSeeTableRecords([$current]);
});

it('creates, edits and deletes a certification', function (): void {
    Livewire::test(CreateCertification::class)
        ->fillForm([
            'name' => 'AWS Solutions Architect',
            'issuer' => 'Amazon Web Services (AWS)',
            'issued_at' => '2025-06-01',
            'credential_url' => 'https://www.credly.com/badges/abc',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $certification = Certification::query()->sole();
    expect($certification->issuer)->toBe('Amazon Web Services (AWS)');

    Livewire::test(EditCertification::class, ['record' => $certification->getRouteKey()])
        ->fillForm(['expires_at' => '2028-06-01'])
        ->call('save')
        ->assertHasNoFormErrors();
    expect($certification->refresh()->expires_at?->toDateString())->toBe('2028-06-01');

    Livewire::test(EditCertification::class, ['record' => $certification->getRouteKey()])->callAction(DeleteAction::class);
    expect(Certification::query()->count())->toBe(0);
});

it('validates the certification form', function (): void {
    Livewire::test(CreateCertification::class)
        ->fillForm([
            'name' => '',
            'issuer' => '',
            'issued_at' => '2025-01-01',
            'expires_at' => '2024-01-01',
            'credential_url' => 'not a url',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'issuer' => 'required',
            'expires_at' => 'after_or_equal',
            'credential_url' => 'url',
        ]);
});
