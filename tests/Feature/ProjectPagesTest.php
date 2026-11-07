<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / ProjectPagesTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Project;

it('shows an empty state when nothing is published', function (): void {
    Project::factory()->draft()->create(['title' => 'Secret']);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertSee('Nothing published yet')
        ->assertDontSee('Secret');
});

it('lists published projects with pagination', function (): void {
    Project::factory()->count(13)->create();

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertViewHas('projects', fn ($projects): bool => $projects->count() === 12 && $projects->total() === 13);
});

it('shows a published project', function (): void {
    $project = Project::factory()->create([
        'title' => 'Pipeline Pro',
        'body' => '## Why it exists',
        'url' => 'https://live.example.com',
        'repository_url' => 'https://github.com/me/pipeline',
        'tech_stack' => ['Docker'],
    ]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('<title>Pipeline Pro · ', false)
        ->assertSee('<h2>Why it exists</h2>', false)
        ->assertSee('https://live.example.com')
        ->assertSee('https://github.com/me/pipeline')
        ->assertSee('Docker');
});

it('hides optional links when they are missing', function (): void {
    $project = Project::factory()->create(['url' => null, 'repository_url' => null, 'tech_stack' => null]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertDontSee('Live site')
        ->assertDontSee('Source code');
});

it('returns 404 for drafts and scheduled projects', function (string $state): void {
    $project = Project::factory()->{$state}()->create();

    $this->get(route('projects.show', $project))->assertNotFound();
})->with(['draft', 'scheduled']);

it('returns 404 for unknown slugs', function (): void {
    $this->get('/projects/does-not-exist')->assertNotFound();
});
