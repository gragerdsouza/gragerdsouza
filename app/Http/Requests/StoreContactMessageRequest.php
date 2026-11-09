<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Email;
use Override;

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
     * @return array<string, list<string|Email>> Validation rules keyed by field.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            // Strict RFC (no quoted local parts etc.) and no look-alike Unicode domains.
            'email' => ['required', 'string', 'max:255', Rule::email()->rfcCompliant(strict: true)->preventSpoofing()],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: browsers submit it empty (→ null); bots tend to fill it.
            'website' => ['prohibited'],
        ];
    }

    /**
     * Where to send the visitor when validation fails: back to the form, not the top of the page.
     *
     * @return string The home page URL anchored at the contact section.
     */
    #[Override]
    protected function getRedirectUrl(): string
    {
        return route('home').'#contact';
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
