<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / ProjectResourceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(fn (): User => actingAsAdmin());

it('lists projects and filters drafts', function (): void {
    $published = Project::factory()->create();
    $draft = Project::factory()->draft()->create();

    Livewire::test(ListProjects::class)
        ->assertCanSeeTableRecords([$published, $draft])
        ->filterTable('published', false)
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$published]);
});

it('only offers the public link for published projects', function (): void {
    $published = Project::factory()->create();
    $draft = Project::factory()->draft()->create();

    Livewire::test(ListProjects::class)
        ->assertActionVisible(TestAction::make('view')->table($published))
        ->assertActionHasUrl(TestAction::make('view')->table($published), route('projects.show', $published))
        ->assertActionHidden(TestAction::make('view')->table($draft));
});

it('creates a project and generates the slug from the title', function (): void {
    Livewire::test(CreateProject::class)
        ->fillForm(['title' => 'Zero Downtime Deploys'])
        ->assertSchemaStateSet(['slug' => 'zero-downtime-deploys'])
        ->fillForm([
            'summary' => 'Blue/green on Laravel Cloud.',
            'tech_stack' => ['Laravel', 'Cloud'],
            'is_featured' => true,
            'published_at' => now()->subHour(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $project = Project::query()->sole();
    expect($project->slug)->toBe('zero-downtime-deploys')
        ->and($project->tech_stack)->toBe(['Laravel', 'Cloud'])
        ->and($project->is_featured)->toBeTrue();
});

it('validates the project form', function (): void {
    Project::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateProject::class)
        ->fillForm(['title' => '', 'slug' => 'taken', 'summary' => '', 'url' => 'not a url'])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'slug' => 'unique', 'summary' => 'required', 'url' => 'url']);
});

it('keeps the slug when editing the title', function (): void {
    $project = Project::factory()->create(['title' => 'Old', 'slug' => 'old']);

    Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
        ->fillForm(['title' => 'New title'])
        ->assertSchemaStateSet(['slug' => 'old'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($project->refresh()->title)->toBe('New title')->and($project->slug)->toBe('old');
});

it('deletes a project', function (): void {
    $project = Project::factory()->create();

    Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(Project::query()->count())->toBe(0);
});
