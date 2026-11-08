<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / HomePageTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;
use App\Models\Certification;
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

it('lists current certifications with issuer, date and credential link', function (): void {
    Certification::factory()->create([
        'name' => 'AWS Cloud Practitioner',
        'issuer' => 'Amazon Web Services (AWS)',
        'issued_at' => '2025-02-01',
        'credential_url' => 'https://www.credly.com/badges/xyz',
    ]);
    Certification::factory()->create(['name' => 'Scrum Master', 'credential_url' => null]);

    $this->get('/')
        ->assertOk()
        ->assertSee('id="certifications" class="scroll-mt-20', false)
        ->assertSeeInOrder(['AWS Cloud Practitioner', 'Amazon Web Services (AWS)', 'Issued Feb 2025'])
        ->assertSee('https://www.credly.com/badges/xyz')
        ->assertSee('Scrum Master');
});

it('never shows expired certifications on the public site', function (): void {
    Certification::factory()->create(['name' => 'Still valid']);
    Certification::factory()->expired()->create(['name' => 'Google Analytics for Beginners']);

    $this->get('/')
        ->assertOk()
        ->assertSee('Still valid')
        ->assertDontSee('Google Analytics for Beginners');
});

it('links to the full list on LinkedIn when a LinkedIn profile is set', function (): void {
    Certification::factory()->create();
    Profile::factory()->create(['social_links' => ['linkedin' => 'https://www.linkedin.com/in/me/']]);

    $this->get('/')
        ->assertOk()
        ->assertSee('href="https://www.linkedin.com/in/me/details/certifications/"', false)
        ->assertSee('See all on LinkedIn');
});

it('omits the LinkedIn link when no LinkedIn profile is set', function (): void {
    Certification::factory()->create();
    Profile::factory()->create(['social_links' => ['github' => 'https://github.com/me']]);

    $this->get('/')->assertOk()->assertDontSee('See all on LinkedIn');
});

it('hides the certifications section when none are current', function (): void {
    Certification::factory()->expired()->create();

    $this->get('/')->assertOk()->assertDontSee('id="certifications"', false);
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

it('never renders a javascript: social link', function (): void {
    Profile::factory()->create(['social_links' => ['github' => 'https://github.com/me', 'evil' => 'javascript:alert(1)']]);

    $this->get('/')
        ->assertSee('href="https://github.com/me"', false)
        ->assertDontSee('javascript:alert(1)', false);
});
