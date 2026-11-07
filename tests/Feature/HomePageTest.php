<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / HomePageTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;

it('renders before any content exists', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('Profile coming soon.')
        ->assertDontSee('Available for new work')
        ->assertDontSee('Featured projects');
});

it('shows the profile, stack, experience and featured projects', function (): void {
    Profile::factory()->create([
        'name' => 'Grager',
        'headline' => 'Full-stack developer',
        'tagline' => 'Tests first.',
        'location' => 'Goa',
        'resume_url' => 'https://example.com/cv.pdf',
        'avatar_path' => 'avatars/me.jpg',
        'social_links' => ['github' => 'https://github.com/grager'],
        'available_for_work' => true,
    ]);
    Skill::factory()->create(['name' => 'Pest', 'category' => SkillCategory::Testing]);
    Experience::factory()->current()->create(['company' => 'Acme', 'role' => 'Lead', 'location' => 'Remote']);
    Experience::factory()->create(['company' => 'OldCo', 'location' => null]);
    Project::factory()->featured()->create(['title' => 'Featured One', 'tech_stack' => ['Laravel']]);
    Project::factory()->featured()->create(['title' => 'No Stack', 'tech_stack' => null]);
    Project::factory()->create(['title' => 'Not Featured']);
    Project::factory()->featured()->draft()->create(['title' => 'Draft Featured']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Grager', 'Full-stack developer', 'Tests first.'])
        ->assertSee('Available for new work')
        ->assertSee('https://example.com/cv.pdf')
        ->assertSee('avatars/me.jpg')
        ->assertSee('https://github.com/grager')
        ->assertSee('Testing &amp; Quality', false)
        ->assertSee('Pest')
        ->assertSee('Lead')
        ->assertSee('Acme')
        ->assertSee('OldCo')
        ->assertSee('Based in Goa')
        ->assertSee('Featured One')
        ->assertSee('No Stack')
        ->assertDontSee('Not Featured')
        ->assertDontSee('Draft Featured');
});

it('sets SEO meta tags', function (): void {
    Profile::factory()->create(['name' => 'Grager', 'headline' => 'Dev', 'tagline' => 'Ships']);

    $this->get('/')
        ->assertSee('<title>Grager — Dev</title>', false)
        ->assertSee('<meta name="description" content="Ships">', false)
        ->assertSee('rel="canonical"', false);
});

it('exposes the health check endpoint', function (): void {
    $this->get('/up')->assertOk();
});
