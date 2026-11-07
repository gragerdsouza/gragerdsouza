<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / ContactMessageTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Date;

it('marks itself as read once', function (): void {
    Date::setTestNow('2026-11-07 10:00:00');
    $message = ContactMessage::factory()->create();

    expect($message->isRead())->toBeFalse();

    $message->markAsRead();
    expect($message->fresh()?->read_at?->toDateTimeString())->toBe('2026-11-07 10:00:00');

    Date::setTestNow('2026-11-08 10:00:00');
    $message->markAsRead();
    expect($message->fresh()?->read_at?->toDateTimeString())->toBe('2026-11-07 10:00:00');
});

it('scopes to unread messages', function (): void {
    $unread = ContactMessage::factory()->create();
    ContactMessage::factory()->read()->create();

    expect(ContactMessage::query()->unread()->pluck('id')->all())->toBe([$unread->id]);
});
