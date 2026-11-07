<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

/**
 * Admin page for creating a skill.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CreateSkill extends CreateRecord
{
    #[Override]
    protected static string $resource = SkillResource::class;
}
