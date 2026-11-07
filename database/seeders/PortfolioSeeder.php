<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SkillCategory;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * Seeds the owner's profile, work experience and skills on a fresh database.
 * Skipped entirely once a profile exists, so it never overwrites edits made in /admin.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void Nothing is returned.
     */
    public function run(): void
    {
        if (Profile::query()->exists()) {
            return;
        }

        Profile::query()->create([
            'name' => 'Grager D\'souza',
            'headline' => 'Full Stack Developer · Test-driven · DevOps-minded',
            'tagline' => 'I build, test and ship web products end to end — from database schema to CI/CD pipeline.',
            'bio' => "I'm a senior full-stack developer who enjoys working across stacks — web, APIs and mobile. Day to day I design, build and ship features end to end, write tests first, and automate the path to production.\n\nAlongside web work I build mobile apps: I designed and delivered a complete digital pass solution for Apple Wallet and Google Wallet on iOS and Android as a solo project.",
            'location' => 'Reading, United Kingdom',
            'email' => 'gragerdsouza@gmail.com',
            'resume_url' => null,
            'social_links' => [
                'github' => 'https://github.com/gragerdsouza',
                'linkedin' => 'https://www.linkedin.com/in/grager-d-souza-462bb2111/',
            ],
            'available_for_work' => false,
        ]);

        $skills = [
            SkillCategory::Backend->value => ['PHP', 'Laravel', 'Node.js', 'REST & GraphQL APIs'],
            SkillCategory::Frontend->value => ['TypeScript', 'Vue', 'React', 'Livewire', 'Tailwind CSS'],
            SkillCategory::Testing->value => ['TDD', 'Pest', 'PHPUnit', 'Playwright', 'Static analysis'],
            SkillCategory::DevOps->value => ['Docker', 'Bitbucket', 'GitHub Actions', 'Laravel Cloud', 'AWS', 'Linux'],
            SkillCategory::Data->value => ['PostgreSQL', 'MySQL', 'Redis'],
        ];

        foreach ($skills as $category => $names) {
            foreach ($names as $order => $name) {
                Skill::query()->create(['name' => $name, 'category' => $category, 'sort_order' => $order]);
            }
        }

        Experience::query()->create([
            'company' => 'Minster Software Limited',
            'role' => 'Senior Full-stack Developer',
            'location' => 'Hybrid',
            'started_at' => '2023-05-03',
            'ended_at' => null,
            'summary' => 'Day to day I design, build and ship features end to end in PHP and Laravel — database, REST APIs and front end — review code, and support the team with technical decisions. I also build mobile apps, and delivered a digital pass solution for Apple Wallet and Google Wallet (iOS and Android) as a solo project, from design to release.',
        ]);
    }
}
