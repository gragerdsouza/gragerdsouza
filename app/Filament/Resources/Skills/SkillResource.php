<?php

declare(strict_types=1);

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Filament\Resources\Skills\Schemas\SkillForm;
use App\Filament\Resources\Skills\Tables\SkillsTable;
use App\Models\Skill;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

/**
 * Filament admin resource for managing skill records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class SkillResource extends Resource
{
    #[Override]
    protected static ?string $model = Skill::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 2;

    /**
     * Build the create/edit form.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function form(Schema $schema): Schema
    {
        return SkillForm::configure($schema);
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
        return SkillsTable::configure($table);
    }

    /**
     * Register the resource's pages and their routes.
     *
     * @return array<string, PageRegistration> Page routes keyed by page name.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSkills::route('/'),
            'create' => CreateSkill::route('/create'),
            'edit' => EditSkill::route('/{record}/edit'),
        ];
    }
}
