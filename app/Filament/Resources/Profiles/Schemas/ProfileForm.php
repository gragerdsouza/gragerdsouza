<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Form schema used to create and edit profile records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProfileForm
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
            ->columns(3)
            ->components([
                Section::make('About you')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('headline')->required()->maxLength(255),
                        TextInput::make('tagline')->maxLength(255)->columnSpanFull(),
                        Textarea::make('bio')->required()->rows(6)->columnSpanFull(),
                    ]),
                Section::make('Contact & links')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('avatar_path')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->maxSize(2048),
                        TextInput::make('email')->email()->required()->maxLength(255),
                        TextInput::make('location')->maxLength(255),
                        TextInput::make('resume_url')->label('Résumé URL')->url()->maxLength(255),
                        Toggle::make('available_for_work'),
                        KeyValue::make('social_links')
                            ->keyLabel('Network')
                            ->valueLabel('URL')
                            ->keyPlaceholder('github'),
                    ]),
            ]);
    }
}
