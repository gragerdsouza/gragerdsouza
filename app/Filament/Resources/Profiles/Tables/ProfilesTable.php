<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Table columns, filters and actions for profiles.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProfilesTable
{
    /**
     * Apply this configuration to the given component.
     *
     * @param Table $table The table to configure.
     *
     * @return Table The configured table.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('headline'),
                IconColumn::make('available_for_work')->label('Available')->boolean(),
                TextColumn::make('updated_at')->since(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
