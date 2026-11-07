<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Dashboard widget summarising messages, projects and skills.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class PortfolioStats extends StatsOverviewWidget
{
    /**
     * The stat cards shown on the dashboard.
     *
     * @return array<Stat> The stat cards.
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Unread messages', ContactMessage::query()->unread()->count())
                ->icon('heroicon-o-inbox')
                ->url(route('filament.admin.resources.contact-messages.index')),
            Stat::make('Published projects', Project::query()->published()->count())
                ->description(Project::query()->whereNull('published_at')->count().' drafts')
                ->icon('heroicon-o-rectangle-stack'),
            Stat::make('Skills listed', Skill::query()->count())
                ->icon('heroicon-o-wrench-screwdriver'),
        ];
    }
}
