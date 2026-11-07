<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Support\Facades\Notification;

/**
 * Persists a contact-form submission and notifies the site owner.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final readonly class SubmitContactMessage
{
    /**
     * Store the submission and email the site owner.
     *
     * @param array{name: string, email: string, subject?: string|null, message: string} $data Validated contact-form fields.
     * @param string|null $ipAddress IP address of the sender, stored for abuse checks.
     *
     * @return ContactMessage The stored message.
     */
    public function handle(array $data, ?string $ipAddress): ContactMessage
    {
        $message = ContactMessage::query()->create([
            ...$data,
            'ip_address' => $ipAddress,
        ]);

        Notification::route('mail', config()->string('portfolio.contact.notify'))
            ->notify(new NewContactMessage($message));

        return $message;
    }
}
