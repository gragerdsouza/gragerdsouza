<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Form schema used to create and edit experience records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ExperienceForm
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
                TextInput::make('role')->required()->maxLength(255),
                TextInput::make('company')->required()->maxLength(255),
                TextInput::make('location')->maxLength(255),
                DatePicker::make('started_at')->label('Started')->required()->native(false),
                DatePicker::make('ended_at')
                    ->label('Ended')
                    ->helperText('Leave empty if this is your current role.')
                    ->afterOrEqual('started_at')
                    ->native(false),
                Textarea::make('summary')->required()->rows(4)->columnSpanFull(),
            ]);
    }
}
