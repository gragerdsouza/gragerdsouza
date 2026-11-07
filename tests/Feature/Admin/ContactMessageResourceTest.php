<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / ContactMessageResourceTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(fn (): User => actingAsAdmin());

it('cannot create messages from the admin', function (): void {
    expect(ContactMessageResource::canCreate())->toBeFalse();
});

it('shows the unread count as a navigation badge', function (): void {
    expect(ContactMessageResource::getNavigationBadge())->toBeNull();

    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->read()->create();

    expect(ContactMessageResource::getNavigationBadge())->toBe('2');
});

it('lists messages and filters by read state', function (): void {
    $unread = ContactMessage::factory()->create();
    $read = ContactMessage::factory()->read()->create();

    Livewire::test(ListContactMessages::class)
        ->assertCanSeeTableRecords([$unread, $read])
        ->filterTable('read_at', false)
        ->assertCanSeeTableRecords([$unread])
        ->assertCanNotSeeTableRecords([$read]);
});

it('marks a message as read when opened and offers a reply link', function (): void {
    $message = ContactMessage::factory()->create(['email' => 'ada@example.com', 'subject' => 'Hi there']);

    Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
        ->assertSee('ada@example.com')
        ->assertActionHasUrl('reply', 'mailto:ada@example.com?subject=Re%3A%20Hi%20there');

    expect($message->refresh()->isRead())->toBeTrue();
});

it('uses a generic reply subject when the message had none', function (): void {
    $message = ContactMessage::factory()->create(['email' => 'ada@example.com', 'subject' => null]);

    Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
        ->assertActionHasUrl('reply', 'mailto:ada@example.com?subject=Re%3A%20your%20message');
});

it('deletes a message', function (): void {
    $message = ContactMessage::factory()->create();

    Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(ContactMessage::query()->count())->toBe(0);
});
