<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Tables;

use App\Models\Certification;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Table columns, filters and actions for certifications.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CertificationsTable
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
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->wrap(),
                TextColumn::make('issuer')->searchable()->sortable(),
                TextColumn::make('issued_at')->label('Issued')->date('M Y')->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date('M Y')
                    ->placeholder('No expiry')
                    ->badge()
                    ->color(fn (Certification $record): string => $record->isExpired() ? 'danger' : 'success'),
            ])
            ->filters([
                TernaryFilter::make('expired')
                    ->trueLabel('Expired')
                    ->falseLabel('Current')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereDate('expires_at', '<', now()),
                        false: fn (Builder $query): Builder => $query->where(fn (Builder $q): Builder => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', now())),
                    ),
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
