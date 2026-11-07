<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

/**
 * Admin page for editing a skill.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class EditSkill extends EditRecord
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
            DeleteAction::make(),
        ];
    }
}
