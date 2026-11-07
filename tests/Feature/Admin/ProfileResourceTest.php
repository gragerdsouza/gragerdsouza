<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / ProfileResourceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use Filament\Actions\CreateAction;
use Livewire\Livewire;

beforeEach(fn (): User => actingAsAdmin());

it('allows creating the profile only once', function (): void {
    expect(ProfileResource::canCreate())->toBeTrue();
    Livewire::test(ListProfiles::class)->assertActionVisible(CreateAction::class);

    Profile::factory()->create();

    expect(ProfileResource::canCreate())->toBeFalse()
        ->and(ProfileResource::canDeleteAny())->toBeFalse();
    Livewire::test(ListProfiles::class)->assertActionHidden(CreateAction::class);
});

it('creates the profile', function (): void {
    Livewire::test(CreateProfile::class)
        ->fillForm([
            'name' => 'Grager',
            'headline' => 'Full-stack developer',
            'bio' => 'I test first.',
            'email' => 'me@example.com',
            'social_links' => ['github' => 'https://github.com/grager'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Profile::current()->social_links)->toBe(['github' => 'https://github.com/grager']);
});

it('edits the profile and the public site reflects it', function (): void {
    $profile = Profile::factory()->create();

    Livewire::test(EditProfile::class, ['record' => $profile->getRouteKey()])
        ->fillForm(['headline' => 'DevOps-minded developer', 'available_for_work' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/')->assertSee('DevOps-minded developer')->assertDontSee('Available for new work');
});

it('validates the profile form', function (): void {
    Livewire::test(CreateProfile::class)
        ->fillForm(['name' => '', 'email' => 'nope', 'resume_url' => 'nope'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'email' => 'email', 'resume_url' => 'url', 'headline' => 'required', 'bio' => 'required']);
});
