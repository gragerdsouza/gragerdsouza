<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / NewContactMessageNotificationTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;

it('is queued and sent by mail', function (): void {
    $notification = new NewContactMessage(ContactMessage::factory()->make());

    expect($notification)->toBeInstanceOf(ShouldQueue::class)
        ->and($notification->via(new AnonymousNotifiable))->toBe(['mail']);
});

it('builds a reply-able email that links to the admin', function (): void {
    $message = ContactMessage::factory()->create([
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'subject' => 'Hello',
        'message' => 'Body text',
    ]);

    $mail = new NewContactMessage($message)->toMail(new AnonymousNotifiable);

    expect($mail->subject)->toBe('New message: Hello')
        ->and($mail->replyTo)->toBe([['ada@example.com', 'Ada']])
        ->and($mail->introLines)->toContain('From: ada@example.com', 'Body text')
        ->and($mail->actionUrl)->toEndWith('/admin/contact-messages/'.$message->id);
});

it('uses a default subject when none was given', function (): void {
    $mail = new NewContactMessage(ContactMessage::factory()->create(['subject' => null]))->toMail(new AnonymousNotifiable);

    expect($mail->subject)->toBe('New message: Portfolio contact form');
});
