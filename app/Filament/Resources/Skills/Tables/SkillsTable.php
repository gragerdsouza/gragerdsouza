<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills\Tables;

use App\Enums\SkillCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Table columns, filters and actions for skills.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SkillsTable
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->defaultGroup('category')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(SkillCategory::class),
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
