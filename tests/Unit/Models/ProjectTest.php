<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / ProjectTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Project;

it('routes by slug', function (): void {
    expect((new Project)->getRouteKeyName())->toBe('slug');
});

it('knows when it is published', function (): void {
    expect(Project::factory()->make()->isPublished())->toBeTrue()
        ->and(Project::factory()->draft()->make()->isPublished())->toBeFalse()
        ->and(Project::factory()->scheduled()->make()->isPublished())->toBeFalse();
});

it('renders markdown and strips raw html', function (): void {
    $project = Project::factory()->make([
        'body' => "## Title\n\n<script>alert(1)</script>\n\n[bad](javascript:alert(1))",
    ]);

    expect($project->bodyHtml())
        ->toContain('<h2>Title</h2>')
        ->not->toContain('<script>')
        ->not->toContain('javascript:');
});

it('renders an empty body', function (): void {
    expect(Project::factory()->make(['body' => null])->bodyHtml())->toBe('');
});

it('scopes to published projects only', function (): void {
    $published = Project::factory()->create();
    Project::factory()->draft()->create();
    Project::factory()->scheduled()->create();

    expect(Project::query()->published()->pluck('id')->all())->toBe([$published->id]);
});

it('scopes to featured projects', function (): void {
    $featured = Project::factory()->featured()->create();
    Project::factory()->create();

    expect(Project::query()->featured()->pluck('id')->all())->toBe([$featured->id]);
});

it('orders by sort order then newest', function (): void {
    $older = Project::factory()->create(['sort_order' => 1, 'published_at' => now()->subWeek()]);
    $newer = Project::factory()->create(['sort_order' => 1, 'published_at' => now()->subDay()]);
    $first = Project::factory()->create(['sort_order' => 0]);

    expect(Project::query()->ordered()->pluck('id')->all())->toBe([$first->id, $newer->id, $older->id]);
});

it('casts the tech stack to an array', function (): void {
    $project = Project::factory()->create(['tech_stack' => ['PHP', 'Go']]);

    expect($project->fresh()?->tech_stack)->toBe(['PHP', 'Go']);
});
