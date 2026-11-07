<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Pages;

use App\Filament\Resources\Certifications\CertificationResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

/**
 * Admin page for creating a certification.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CreateCertification extends CreateRecord
{
    #[Override]
    protected static string $resource = CertificationResource::class;
}
