<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Pages;

use App\Filament\Resources\Certifications\CertificationResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

/**
 * Admin page listing certifications.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ListCertifications extends ListRecords
{
    #[Override]
    protected static string $resource = CertificationResource::class;

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
