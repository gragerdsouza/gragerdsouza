<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SkillCategory;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * Starter content so the site looks complete on first deploy. Edit it all in /admin.
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
            'name' => "Grager D'souza",
            'headline' => 'Full-stack developer · Test-driven · DevOps-minded',
            'tagline' => 'I build, test and ship web products end to end — from database schema to CI/CD pipeline.',
            'bio' => "I'm a full-stack developer who enjoys working across stacks. I write tests first, automate everything I touch, and care about the whole path to production: clean architecture, fast feedback loops and boring, reliable deployments.",
            'location' => 'Remote',
            'email' => 'hello@example.com',
            'social_links' => [
                'github' => 'https://github.com/',
                'linkedin' => 'https://www.linkedin.com/',
            ],
            'available_for_work' => true,
        ]);

        $skills = [
            SkillCategory::Backend->value => ['PHP', 'Laravel', 'Node.js', 'REST & GraphQL APIs'],
            SkillCategory::Frontend->value => ['TypeScript', 'Vue', 'React', 'Livewire', 'Tailwind CSS'],
            SkillCategory::Testing->value => ['TDD', 'Pest', 'PHPUnit', 'Playwright', 'Static analysis'],
            SkillCategory::DevOps->value => ['Docker', 'GitHub Actions', 'Laravel Cloud', 'AWS', 'Linux'],
            SkillCategory::Data->value => ['PostgreSQL', 'MySQL', 'Redis'],
        ];

        foreach ($skills as $category => $names) {
            foreach ($names as $order => $name) {
                Skill::query()->create(['name' => $name, 'category' => $category, 'sort_order' => $order]);
            }
        }

        Experience::query()->create([
            'company' => 'Your current company',
            'role' => 'Senior Full-stack Developer',
            'location' => 'Remote',
            'started_at' => '2022-01-01',
            'ended_at' => null,
            'summary' => 'Replace this with what you do — products shipped, pipelines built, teams helped.',
        ]);
    }
}
