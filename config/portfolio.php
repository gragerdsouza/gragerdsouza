<?php

declare(strict_types=1);

/**
 * Configuration: portfolio.php.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Admin account
    |--------------------------------------------------------------------------
    |
    | Used by `php artisan db:seed --class=AdminUserSeeder` (and the default
    | DatabaseSeeder) to create or update the Filament admin user. The password
    | is only applied when set, so re-seeding never resets it to something weak.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    */

    'contact' => [
        // Where new contact-form messages are emailed. Falls back to the admin email.
        'notify' => env('CONTACT_NOTIFY_EMAIL') ?: env('ADMIN_EMAIL', 'admin@example.com'),
        // Max submissions per IP per minute.
        'per_minute' => (int) env('CONTACT_RATE_LIMIT', 3),
    ],

];
