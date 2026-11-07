<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / SeederTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Certification;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CertificationSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PortfolioSeeder;
use Database\Seeders\ProjectSeeder;
use Illuminate\Support\Facades\Hash;

it('creates the admin with the configured password', function (): void {
    config(['portfolio.admin' => ['name' => 'Owner', 'email' => 'owner@example.com', 'password' => 's3cret-pass']]);

    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'owner@example.com')->sole();
    expect($admin->is_admin)->toBeTrue()
        ->and($admin->name)->toBe('Owner')
        ->and(Hash::check('s3cret-pass', $admin->password))->toBeTrue();
});

it('creates the admin with a random password when none is configured', function (): void {
    config(['portfolio.admin' => ['name' => 'Owner', 'email' => 'owner@example.com', 'password' => null]]);

    $this->artisan('db:seed', ['--class' => AdminUserSeeder::class])
        ->expectsOutputToContain('ADMIN_PASSWORD not set')
        ->assertSuccessful();

    expect(User::query()->where('email', 'owner@example.com')->sole()->is_admin)->toBeTrue();
});

it('promotes an existing user without resetting their password', function (): void {
    $user = User::factory()->create(['email' => 'owner@example.com', 'password' => 'keep-me']);
    config(['portfolio.admin' => ['name' => 'Owner', 'email' => 'owner@example.com', 'password' => '']]);

    $this->seed(AdminUserSeeder::class);

    $user->refresh();
    expect($user->is_admin)->toBeTrue()
        ->and(Hash::check('keep-me', $user->password))->toBeTrue()
        ->and(User::query()->count())->toBe(1);
});

it('seeds starter portfolio content once', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->seed(PortfolioSeeder::class);

    expect(Profile::query()->count())->toBe(1)
        ->and(Skill::query()->count())->toBeGreaterThan(10)
        ->and(Experience::query()->count())->toBe(1)
        ->and(Project::query()->published()->featured()->count())->toBe(5)
        ->and(User::query()->where('is_admin', true)->count())->toBe(1);

    $this->get('/')->assertOk()->assertSee('Grager D&#039;souza', false);
});

it('seeds a curated set of LinkedIn certifications once', function (): void {
    $this->seed(CertificationSeeder::class);
    $this->seed(CertificationSeeder::class);

    expect(Certification::query()->orderBy('name')->pluck('name')->all())->toBe([
        'Amazon Web Services Cloud Practitioner',
        'Fundamentals of Agile Project Management',
        'Scrum Master Certification: Scrum Methodologies',
    ])->and(Certification::query()->current()->count())->toBe(3);
});

it('seeds every portfolio project, published and in display order', function (): void {
    $this->seed(ProjectSeeder::class);

    expect(Project::query()->published()->ordered()->pluck('slug')->all())->toBe([
        'digital-wallet-passes',
        'vehicle-data-service',
        'whatsapp-hub',
        'native-mobile-app',
        'this-portfolio',
    ])
        ->and(Project::query()->where('slug', 'vehicle-data-service')->sole()->tech_stack)->toContain('PHP', 'Laravel')
        ->and(Project::query()->where('slug', 'whatsapp-hub')->sole()->url)->toBeNull()
        ->and(Project::query()->where('body', 'like', '%ClearVehicle%')->exists())->toBeFalse();
});

it('never overwrites projects edited in the admin when re-seeded', function (): void {
    $this->seed(ProjectSeeder::class);
    Project::query()->where('slug', 'whatsapp-hub')->update(['title' => 'Edited in admin']);

    $this->seed(ProjectSeeder::class);

    expect(Project::query()->count())->toBe(5)
        ->and(Project::query()->where('slug', 'whatsapp-hub')->sole()->title)->toBe('Edited in admin');
});
