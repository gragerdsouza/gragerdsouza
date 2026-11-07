<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emails the site owner when someone submits the contact form.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Construct.
     *
     * @param ContactMessage $contactMessage The message that was submitted.
     */
    public function __construct(public readonly ContactMessage $contactMessage) {}

    /**
     * The notification's delivery channels.
     *
     * @param object $notifiable The entity receiving the notification.
     *
     * @return list<string> Channel names.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the email sent to the site owner.
     *
     * @param object $notifiable The entity receiving the notification.
     *
     * @return MailMessage The email to send.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->contactMessage;

        return (new MailMessage)
            ->subject('New message: '.($message->subject ?? 'Portfolio contact form'))
            ->replyTo($message->email, $message->name)
            ->greeting('New message from '.$message->name)
            ->line('From: '.$message->email)
            ->line($message->message)
            ->action('Open in admin', route('filament.admin.resources.contact-messages.view', $message));
    }
}
