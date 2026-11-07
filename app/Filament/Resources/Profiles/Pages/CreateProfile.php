<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

/**
 * Admin page for creating a profile.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CreateProfile extends CreateRecord
{
    #[Override]
    protected static string $resource = ProfileResource::class;
}
