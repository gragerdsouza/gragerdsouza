<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/**
 * Table columns, filters and actions for projects.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProjectsTable
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
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')->label('Featured')->boolean(),
                TextColumn::make('published_at')->label('Published')->dateTime()->sortable()->placeholder('Draft'),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('published')
                    ->nullable()
                    ->attribute('published_at')
                    ->trueLabel('Published')
                    ->falseLabel('Drafts'),
                TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Project $record): string => route('projects.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Project $record): bool => $record->isPublished()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
