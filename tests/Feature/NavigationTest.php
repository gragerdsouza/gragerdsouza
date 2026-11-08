<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / NavigationTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;

beforeEach(function (): void {
    Profile::factory()->create();
    Skill::factory()->create();
    Experience::factory()->create();
    Project::factory()->featured()->create();
});

it('links every home section with host-independent anchors', function (string $page): void {
    $response = $this->get($page)->assertOk();

    foreach (['about', 'stack', 'experience', 'contact'] as $section) {
        $response->assertSee('href="/#'.$section.'"', false);
    }
})->with(['/', '/projects']);

it('collapses the menu behind an accessible hamburger button on small screens', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('data-menu-toggle', false)
        ->assertSee('aria-controls="site-menu"', false)
        ->assertSee('aria-expanded="false"', false)
        ->assertSee('aria-label="Open menu"', false)
        ->assertSee('id="site-menu"', false)
        ->assertDontSee('<li class="hidden', false);
});

it('shows the full site name on every screen size', function (): void {
    Profile::query()->update(['name' => "Grager D'souza"]);

    $this->get('/')
        ->assertOk()
        ->assertSee('<span class="truncate">grager-dsouza</span>', false);
});

it('gives every anchored section an id and a scroll offset for the sticky header', function (string $section): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="'.$section.'" class="scroll-mt-20', false);
})->with(['about', 'stack', 'experience', 'projects', 'contact']);

it('marks nav links for the active-section highlighter', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('data-nav-link href="/#stack"', false);
});

it('marks the projects page link as current on project pages', function (): void {
    $html = $this->get('/projects')->assertOk()->getContent();

    expect($html)->toMatch('#href="'.preg_quote(route('projects.index'), '#').'"\s+aria-current="page"#');

    $this->get('/')
        ->assertOk()
        ->assertDontSee('aria-current="page"', false);
});
