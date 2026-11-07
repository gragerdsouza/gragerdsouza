<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;
use Override;

/**
 * Admin page listing contact messages.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ListContactMessages extends ListRecords
{
    #[Override]
    protected static string $resource = ContactMessageResource::class;
}
