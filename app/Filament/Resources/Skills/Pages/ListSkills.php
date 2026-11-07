<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

/**
 * Admin page listing skills.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ListSkills extends ListRecords
{
    #[Override]
    protected static string $resource = SkillResource::class;

    /**
     * Actions shown in the page header.
     *
     * @return array<Action|ActionGroup> The header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
