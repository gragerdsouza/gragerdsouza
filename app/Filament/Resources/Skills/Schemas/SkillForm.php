<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills\Schemas;

use App\Enums\SkillCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Form schema used to create and edit skill records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SkillForm
{
    /**
     * Apply this configuration to the given component.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(80),
                Select::make('category')->options(SkillCategory::class)->required(),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
            ]);
    }
}
