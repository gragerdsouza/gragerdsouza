<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Override;

/**
 * Admin page for viewing a contact message.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ViewContactMessage extends ViewRecord
{
    #[Override]
    protected static string $resource = ContactMessageResource::class;

    /**
     * Load the record when the page is opened.
     *
     * @param int|string $record Route key of the record to load.
     *
     * @return void Nothing is returned.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->contactMessage()->markAsRead();
    }

    /**
     * Actions shown in the page header.
     *
     * @return array<Action|ActionGroup> The header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url(fn (): string => 'mailto:'.$this->contactMessage()->email.'?subject='.rawurlencode('Re: '.($this->contactMessage()->subject ?? 'your message'))),
            DeleteAction::make(),
        ];
    }

    /**
     * The record being viewed, typed as a contact message.
     *
     * @return ContactMessage The current record.
     */
    private function contactMessage(): ContactMessage
    {
        /** @var ContactMessage */
        return $this->getRecord();
    }
}
