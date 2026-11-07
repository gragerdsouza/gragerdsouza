<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications;

use App\Filament\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Resources\Certifications\Pages\EditCertification;
use App\Filament\Resources\Certifications\Pages\ListCertifications;
use App\Filament\Resources\Certifications\Schemas\CertificationForm;
use App\Filament\Resources\Certifications\Tables\CertificationsTable;
use App\Models\Certification;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

/**
 * Filament admin resource for managing certification records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class CertificationResource extends Resource
{
    #[Override]
    protected static ?string $model = Certification::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 4;

    /**
     * Build the create/edit form.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function form(Schema $schema): Schema
    {
        return CertificationForm::configure($schema);
    }

    /**
     * Build the list table.
     *
     * @param Table $table The table to configure.
     *
     * @return Table The configured table.
     */
    public static function table(Table $table): Table
    {
        return CertificationsTable::configure($table);
    }

    /**
     * Register the resource's pages and their routes.
     *
     * @return array<string, PageRegistration> Page routes keyed by page name.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListCertifications::route('/'),
            'create' => CreateCertification::route('/create'),
            'edit' => EditCertification::route('/{record}/edit'),
        ];
    }
}
