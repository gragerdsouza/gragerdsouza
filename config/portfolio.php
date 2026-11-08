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

        // URL prefix of the admin panel. A non-obvious path cuts automated login probing.
        'path' => env('ADMIN_PATH', 'admin'),

        // Comma-separated IPs / CIDR ranges allowed to reach the admin. Everyone else
        // gets a 404. Empty = no restriction (2FA still protects the login).
        'allowed_ips' => array_values(array_filter(array_map(
            trim(...),
            explode(',', (string) env('ADMIN_ALLOWED_IPS', '')),
        ))),

        // Force the admin to set up an authenticator app before using the panel.
        'require_mfa' => (bool) env('ADMIN_REQUIRE_MFA', true),
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

    /*
    |--------------------------------------------------------------------------
    | Public site
    |--------------------------------------------------------------------------
    */

    'public' => [
        // Max page requests per IP per minute; blunts scrapers and floods.
        'per_minute' => (int) env('PUBLIC_RATE_LIMIT', 120),
    ],

];
