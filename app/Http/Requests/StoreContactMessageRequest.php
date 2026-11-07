<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates contact-form input, including the honeypot field.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class StoreContactMessageRequest extends FormRequest
{
    /**
     * The validation rules for the request.
     *
     * @return array<string, list<string>> Validation rules keyed by field.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: hidden from humans, bots tend to fill it.
            'website' => ['missing'],
        ];
    }

    /**
     * The validated fields to persist.
     *
     * @return array{name: string, email: string, subject: string|null, message: string} Name, email, subject and message.
     */
    public function payload(): array
    {
        /** @var array{name: string, email: string, subject: string|null, message: string} */
        return $this->safe()->only(['name', 'email', 'subject', 'message']);
    }
}
