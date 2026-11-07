<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences;

use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Resources\Experiences\Schemas\ExperienceForm;
use App\Filament\Resources\Experiences\Tables\ExperiencesTable;
use App\Models\Experience;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

/**
 * Filament admin resource for managing experience records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ExperienceResource extends Resource
{
    #[Override]
    protected static ?string $model = Experience::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 3;

    /**
     * Build the create/edit form.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function form(Schema $schema): Schema
    {
        return ExperienceForm::configure($schema);
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
        return ExperiencesTable::configure($table);
    }

    /**
     * Register the resource's pages and their routes.
     *
     * @return array<string, PageRegistration> Page routes keyed by page name.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListExperiences::route('/'),
            'create' => CreateExperience::route('/create'),
            'edit' => EditExperience::route('/{record}/edit'),
        ];
    }
}
