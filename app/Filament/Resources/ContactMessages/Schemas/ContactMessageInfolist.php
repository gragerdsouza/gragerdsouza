<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Read-only view schema for a contact message.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ContactMessageInfolist
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
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('created_at')->label('Received')->dateTime(),
                        TextEntry::make('subject')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('message')->columnSpanFull()->prose(),
                        TextEntry::make('ip_address')->label('IP address')->placeholder('—'),
                        TextEntry::make('read_at')->label('Read')->dateTime()->placeholder('Unread'),
                    ]),
            ]);
    }
}
