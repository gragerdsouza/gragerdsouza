<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

/**
 * Admin page listing profiles.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ListProfiles extends ListRecords
{
    #[Override]
    protected static string $resource = ProfileResource::class;

    /**
     * Actions shown in the page header.
     *
     * @return array<Action|ActionGroup> The header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->visible(fn (): bool => ProfileResource::canCreate()),
        ];
    }
}
