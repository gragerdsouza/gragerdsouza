<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / LlmsTxtTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;
use App\Models\Certification;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;

it('describes the site for language models in llms.txt markdown', function (): void {
    Profile::factory()->create([
        'name' => 'Grager D\'souza',
        'headline' => 'Full-stack developer',
        'tagline' => 'Laravel, testing and DevOps.',
        'bio' => "I build tested software.\n\nI ship with CI.",
        'location' => 'Toronto',
        'social_links' => ['github' => 'https://github.com/me', 'evil' => 'javascript:alert(1)'],
    ]);
    $project = Project::factory()->create(['title' => 'Portfolio', 'slug' => 'portfolio', 'summary' => "Laravel site\nwith admin."]);
    Project::factory()->draft()->create(['title' => 'Secret draft', 'slug' => 'secret-draft']);
    Skill::factory()->create(['name' => 'Laravel', 'category' => SkillCategory::Backend]);
    Experience::factory()->create(['company' => 'Acme', 'role' => 'Senior Developer']);
    Certification::factory()->create(['name' => 'AWS Developer', 'issuer' => 'Amazon']);

    $response = $this->get('/llms.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    expect($response->getContent())
        ->toStartWith("# Grager D'souza\n\n> Full-stack developer. Laravel, testing and DevOps.\n\nI build tested software. I ship with CI.\n")
        ->toContain('Based in Toronto.')
        ->toContain('- [Portfolio]('.route('projects.show', $project).'): Laravel site with admin.')
        ->toContain('- Backend: Laravel')
        ->toContain('- Senior Developer at Acme')
        ->toContain('- AWS Developer (Amazon)')
        ->toContain('- [github](https://github.com/me)')
        ->toContain('- [Sitemap]('.route('sitemap').')')
        ->not->toContain('Secret draft')
        ->not->toContain('javascript:')
        ->not->toContain('&#039;');
});

it('renders before the profile is set up', function (): void {
    config()->set('app.name', 'My Portfolio');

    expect($this->get('/llms.txt')->assertOk()->getContent())
        ->toStartWith("# My Portfolio\n\n> Full-stack developer.\n\nProfile coming soon.\n");
});
