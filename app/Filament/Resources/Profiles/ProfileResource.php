<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles;

use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Profile;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

/**
 * The site has exactly one profile: it can be created once, then only edited.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProfileResource extends Resource
{
    #[Override]
    protected static ?string $model = Profile::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 0;

    /**
     * Whether a new record may be created.
     *
     * @return bool True when creating is allowed.
     */
    public static function canCreate(): bool
    {
        return ! Profile::query()->exists();
    }

    /**
     * Whether records may be bulk deleted.
     *
     * @return bool Always false: the profile cannot be deleted.
     */
    public static function canDeleteAny(): bool
    {
        return false;
    }

    /**
     * Build the create/edit form.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
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
        return ProfilesTable::configure($table);
    }

    /**
     * Register the resource's pages and their routes.
     *
     * @return array<string, PageRegistration> Page routes keyed by page name.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'create' => CreateProfile::route('/create'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }
}
