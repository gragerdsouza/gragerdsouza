<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Table columns, filters and actions for experiences.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ExperiencesTable
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
            ->defaultSort('started_at', 'desc')
            ->columns([
                TextColumn::make('role')->searchable(),
                TextColumn::make('company')->searchable(),
                TextColumn::make('started_at')->label('Started')->date('M Y')->sortable(),
                TextColumn::make('ended_at')->label('Ended')->date('M Y')->placeholder('Present')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
