<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessages\Schemas\ContactMessageInfolist;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

/**
 * Read-only inbox for contact-form submissions.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ContactMessageResource extends Resource
{
    #[Override]
    protected static ?string $model = ContactMessage::class;

    #[Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    #[Override]
    protected static string|UnitEnum|null $navigationGroup = 'Inbox';

    #[Override]
    protected static ?string $navigationLabel = 'Messages';

    /**
     * Number of unread messages shown next to the menu item.
     *
     * @return string|null The unread count, or null when there is none.
     */
    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::query()->unread()->count();

        return $unread > 0 ? (string) $unread : null;
    }

    /**
     * Whether a new record may be created.
     *
     * @return bool True when creating is allowed.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Build the read-only record view.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
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
        return ContactMessagesTable::configure($table);
    }

    /**
     * Register the resource's pages and their routes.
     *
     * @return array<string, PageRegistration> Page routes keyed by page name.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
