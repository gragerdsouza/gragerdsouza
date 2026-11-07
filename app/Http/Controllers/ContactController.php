<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SubmitContactMessage;
use App\Http\Requests\StoreContactMessageRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Accepts contact-form submissions from the public site.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ContactController
{
    /**
     * Store a contact message and redirect back to the form.
     *
     * @param StoreContactMessageRequest $request The incoming HTTP request.
     * @param SubmitContactMessage $submit Action that stores the message and notifies the owner.
     *
     * @return RedirectResponse Redirect back to the contact form.
     */
    public function store(StoreContactMessageRequest $request, SubmitContactMessage $submit): RedirectResponse
    {
        $submit->handle($request->payload(), $request->ip());

        return redirect()->to(route('home').'#contact')->with('status', "Thanks — your message is in. I'll get back to you soon.");
    }
}
