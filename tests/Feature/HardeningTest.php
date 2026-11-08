<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / HardeningTest.
 *
 * Password policy, public rate limiting and session cookie settings.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Whether the given password passes the application's default password rules.
 *
 * @param string $password The candidate password.
 *
 * @return bool True when the password is accepted.
 */
function passwordIsAccepted(string $password): bool
{
    return Validator::make(['password' => $password], ['password' => [Password::default()]])->passes();
}

it('requires long, mixed passwords that are not in known breaches in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);

    expect(passwordIsAccepted('password'))->toBeFalse()
        ->and(passwordIsAccepted('alllowercase-long-password'))->toBeFalse()
        ->and(passwordIsAccepted('Str0ng!Passw0rd-2026'))->toBeTrue();

    Http::assertSent(fn ($request): bool => str_starts_with((string) $request->url(), 'https://api.pwnedpasswords.com/range/'));
});

it('rejects a strong-looking password that appears in a data breach in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');
    $suffix = strtoupper(substr(sha1('Str0ng!Passw0rd-2026'), 5));
    Http::fake(['api.pwnedpasswords.com/*' => Http::response("{$suffix}:42")]);

    expect(passwordIsAccepted('Str0ng!Passw0rd-2026'))->toBeFalse();
});

it('keeps a simple password policy outside production', function (): void {
    expect(passwordIsAccepted('password'))->toBeTrue()
        ->and(passwordIsAccepted('short'))->toBeFalse();
});

it('rate limits the public site per IP', function (): void {
    config()->set('portfolio.public.per_minute', 2);

    $this->get('/')->assertOk();
    $this->get('/projects')->assertOk();
    $this->get('/sitemap.xml')->assertTooManyRequests();
});

it('leaves the session cookie usable over plain http in local development', function (): void {
    $config = configWithEnv('session.php', ['APP_ENV' => 'local', 'SESSION_SECURE_COOKIE' => null]);

    expect($config['secure'])->toBeFalse();
});

it('encrypts sessions and marks cookies secure in production by default', function (): void {
    $config = configWithEnv('session.php', ['APP_ENV' => 'production', 'SESSION_SECURE_COOKIE' => null, 'SESSION_ENCRYPT' => null]);

    expect($config['secure'])->toBeTrue()
        ->and($config['encrypt'])->toBeTrue()
        ->and($config['http_only'])->toBeTrue()
        ->and($config['same_site'])->toBe('lax');
});
