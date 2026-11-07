<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / DashboardTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Filament\Widgets\PortfolioStats;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use Livewire\Livewire;

it('summarises the portfolio', function (): void {
    actingAsAdmin();
    ContactMessage::factory()->count(2)->create();
    Project::factory()->count(3)->create();
    Project::factory()->draft()->create();
    Skill::factory()->count(4)->create();

    Livewire::test(PortfolioStats::class)
        ->assertSeeInOrder(['Unread messages', '2', 'Published projects', '3', '1 drafts', 'Skills listed', '4']);
});
