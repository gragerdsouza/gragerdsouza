<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Form schema used to create and edit certification records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CertificationForm
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
                TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('issuer')->required()->maxLength(255)->placeholder('Amazon Web Services (AWS)'),
                TextInput::make('credential_url')->label('Credential URL')->url()->maxLength(255),
                DatePicker::make('issued_at')->label('Issued')->required()->native(false)->displayFormat('M Y'),
                DatePicker::make('expires_at')
                    ->label('Expires')
                    ->helperText('Leave empty if it does not expire.')
                    ->afterOrEqual('issued_at')
                    ->native(false)
                    ->displayFormat('M Y'),
            ]);
    }
}
