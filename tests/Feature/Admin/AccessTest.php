<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / AccessTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Certification;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;

dataset('admin pages', [
    'dashboard' => fn (): string => '/admin',
    'profiles' => fn (): string => '/admin/profiles',
    'profile create' => fn (): string => '/admin/profiles/create',
    'profile edit' => fn (): string => '/admin/profiles/'.Profile::factory()->create()->id.'/edit',
    'projects' => fn (): string => '/admin/projects',
    'project create' => fn (): string => '/admin/projects/create',
    'project edit' => fn (): string => '/admin/projects/'.Project::factory()->create()->slug.'/edit',
    'skills' => fn (): string => '/admin/skills',
    'skill create' => fn (): string => '/admin/skills/create',
    'skill edit' => fn (): string => '/admin/skills/'.Skill::factory()->create()->id.'/edit',
    'experiences' => fn (): string => '/admin/experiences',
    'experience create' => fn (): string => '/admin/experiences/create',
    'experience edit' => fn (): string => '/admin/experiences/'.Experience::factory()->create()->id.'/edit',
    'certifications' => fn (): string => '/admin/certifications',
    'certification create' => fn (): string => '/admin/certifications/create',
    'certification edit' => fn (): string => '/admin/certifications/'.Certification::factory()->create()->id.'/edit',
    'messages' => fn (): string => '/admin/contact-messages',
    'message view' => fn (): string => '/admin/contact-messages/'.ContactMessage::factory()->create()->id,
]);

it('redirects guests to the login page', function (string $url): void {
    $this->get($url)->assertRedirect('/admin/login');
})->with('admin pages');

it('forbids non-admin users', function (string $url): void {
    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
})->with('admin pages');

it('renders every page for admins', function (string $url): void {
    actingAsAdmin();

    $this->get($url)->assertOk();
})->with('admin pages');

it('shows the login page', function (): void {
    $this->get('/admin/login')->assertOk();
});
