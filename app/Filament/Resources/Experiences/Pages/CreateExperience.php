<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

/**
 * Admin page for creating a experience.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CreateExperience extends CreateRecord
{
    #[Override]
    protected static string $resource = ExperienceResource::class;
}
