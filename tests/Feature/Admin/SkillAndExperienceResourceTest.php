<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / SkillAndExperienceResourceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(fn (): User => actingAsAdmin());

it('lists and filters skills by category', function (): void {
    $php = Skill::factory()->create(['category' => SkillCategory::Backend]);
    $docker = Skill::factory()->create(['category' => SkillCategory::DevOps]);

    Livewire::test(ListSkills::class)
        ->assertCanSeeTableRecords([$php, $docker])
        ->filterTable('category', SkillCategory::DevOps->value)
        ->assertCanSeeTableRecords([$docker])
        ->assertCanNotSeeTableRecords([$php]);
});

it('creates, edits and deletes a skill', function (): void {
    Livewire::test(CreateSkill::class)
        ->fillForm(['name' => 'Kubernetes', 'category' => SkillCategory::DevOps->value, 'sort_order' => 3])
        ->call('create')
        ->assertHasNoFormErrors();

    $skill = Skill::query()->sole();
    expect($skill->category)->toBe(SkillCategory::DevOps);

    Livewire::test(EditSkill::class, ['record' => $skill->getRouteKey()])
        ->fillForm(['name' => 'K8s'])
        ->call('save')
        ->assertHasNoFormErrors();
    expect($skill->refresh()->name)->toBe('K8s');

    Livewire::test(EditSkill::class, ['record' => $skill->getRouteKey()])->callAction(DeleteAction::class);
    expect(Skill::query()->count())->toBe(0);
});

it('requires a skill name and category', function (): void {
    Livewire::test(CreateSkill::class)
        ->fillForm(['name' => '', 'category' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'category' => 'required']);
});

it('lists experiences', function (): void {
    $experiences = Experience::factory()->count(2)->create();

    Livewire::test(ListExperiences::class)->assertCanSeeTableRecords($experiences);
});

it('creates, edits and deletes an experience', function (): void {
    Livewire::test(CreateExperience::class)
        ->fillForm([
            'role' => 'Platform Engineer',
            'company' => 'Acme',
            'started_at' => '2022-01-01',
            'summary' => 'Built the CI/CD platform.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $experience = Experience::query()->sole();
    expect($experience->isCurrent())->toBeTrue();

    Livewire::test(EditExperience::class, ['record' => $experience->getRouteKey()])
        ->fillForm(['ended_at' => '2024-06-30'])
        ->call('save')
        ->assertHasNoFormErrors();
    expect($experience->refresh()->isCurrent())->toBeFalse();

    Livewire::test(EditExperience::class, ['record' => $experience->getRouteKey()])->callAction(DeleteAction::class);
    expect(Experience::query()->count())->toBe(0);
});

it('rejects an end date before the start date', function (): void {
    Livewire::test(CreateExperience::class)
        ->fillForm([
            'role' => 'Dev',
            'company' => 'Acme',
            'started_at' => '2024-01-01',
            'ended_at' => '2023-01-01',
            'summary' => 'Time travel.',
        ])
        ->call('create')
        ->assertHasFormErrors(['ended_at' => 'after_or_equal']);
});
