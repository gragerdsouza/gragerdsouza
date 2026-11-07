<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

/**
 * Admin page for creating a project.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CreateProject extends CreateRecord
{
    #[Override]
    protected static string $resource = ProjectResource::class;
}
