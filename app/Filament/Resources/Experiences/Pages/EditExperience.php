<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

/**
 * Admin page for editing a experience.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class EditExperience extends EditRecord
{
    #[Override]
    protected static string $resource = ExperienceResource::class;

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
