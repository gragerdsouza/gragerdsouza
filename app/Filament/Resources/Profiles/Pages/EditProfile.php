<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\EditRecord;
use Override;

/**
 * Admin page for editing a profile.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class EditProfile extends EditRecord
{
    #[Override]
    protected static string $resource = ProfileResource::class;
}
