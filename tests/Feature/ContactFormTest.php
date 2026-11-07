<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / ContactFormTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    Notification::fake();
});

/**
 * A valid contact-form payload, with optional overrides.
 *
 * @param array $overrides Fields to replace in the default payload.
 *
 * @return array<mixed> The request payload.
 */
function validContact(array $overrides = []): array
{
    return [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'subject' => 'Hiring',
        'message' => 'We would love to talk about a role.',
        ...$overrides,
    ];
}

it('stores the message and notifies the owner', function (): void {
    $this->post(route('contact.store'), validContact())
        ->assertRedirect(route('home').'#contact')
        ->assertSessionHas('status');

    $message = ContactMessage::query()->sole();

    expect($message->name)->toBe('Ada Lovelace')
        ->and($message->subject)->toBe('Hiring')
        ->and($message->ip_address)->toBe('127.0.0.1')
        ->and($message->isRead())->toBeFalse();

    Notification::assertSentTo(
        new AnonymousNotifiable,
        NewContactMessage::class,
        fn (NewContactMessage $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'owner@example.com'
            && $notification->contactMessage->is($message),
    );
});

it('accepts a real browser submission that includes the empty honeypot field', function (): void {
    $this->post(route('contact.store'), validContact(['website' => '']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home').'#contact');

    expect(ContactMessage::query()->count())->toBe(1);
});

it('sends the visitor back to the form section when validation fails', function (): void {
    $this->from(route('home'))
        ->post(route('contact.store'), validContact(['email' => 'nope']))
        ->assertRedirect(route('home').'#contact')
        ->assertSessionHasErrors('email');
});

it('shows the success message after redirect', function (): void {
    $this->followingRedirects()
        ->post(route('contact.store'), validContact())
        ->assertSee('your message is in', false);
});

it('accepts a message without a subject', function (): void {
    $this->post(route('contact.store'), validContact(['subject' => null]))->assertSessionHasNoErrors();

    expect(ContactMessage::query()->sole()->subject)->toBeNull();
});

it('validates input', function (array $input, string $field): void {
    $this->from(route('home'))
        ->post(route('contact.store'), validContact($input))
        ->assertRedirect(route('home').'#contact')
        ->assertSessionHasErrors($field);

    expect(ContactMessage::query()->count())->toBe(0);
    Notification::assertNothingSent();
})->with([
    'missing name' => [['name' => ''], 'name'],
    'long name' => [['name' => str_repeat('a', 121)], 'name'],
    'missing email' => [['email' => ''], 'email'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'long subject' => [['subject' => str_repeat('a', 161)], 'subject'],
    'short message' => [['message' => 'hi'], 'message'],
    'long message' => [['message' => str_repeat('a', 5001)], 'message'],
    'honeypot filled' => [['website' => 'https://spam.example'], 'website'],
]);

it('re-renders errors and old input on the form', function (): void {
    $this->followingRedirects()
        ->from(route('home'))
        ->post(route('contact.store'), validContact(['email' => 'nope', 'name' => 'Old Name']))
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('value="Old Name"', false);
});

it('rate limits submissions per ip', function (): void {
    foreach (range(1, 3) as $ignored) {
        $this->post(route('contact.store'), validContact())->assertRedirect();
    }

    $this->post(route('contact.store'), validContact())->assertTooManyRequests();
    expect(ContactMessage::query()->count())->toBe(3);
});
