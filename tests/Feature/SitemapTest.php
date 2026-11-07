<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / SitemapTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Project;

it('lists public urls as xml', function (): void {
    $published = Project::factory()->create(['slug' => 'live-one']);
    Project::factory()->draft()->create(['slug' => 'hidden-one']);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
        ->assertSee(route('projects.show', $published), false)
        ->assertDontSee('hidden-one');
});
