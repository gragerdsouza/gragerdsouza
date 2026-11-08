<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / SecurityHeadersTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;

it('sends hardening headers', function (): void {
    $this->get('/')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
        ->assertHeaderMissing('X-Powered-By')
        ->assertHeaderMissing('Strict-Transport-Security');
});

it('sends HSTS over https', function (): void {
    $this->get('https://localhost/')
        ->assertHeader('Strict-Transport-Security', 'max-age=63072000; includeSubDomains');
});

it('sends a strict nonce-based content security policy on public pages', function (): void {
    $policy = $this->get('/')->headers->get('Content-Security-Policy');
    $nonce = Vite::cspNonce();

    expect($nonce)->toBeString()->not->toBeEmpty()
        ->and($policy)->toBe(implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
        ]));
});

it('uses a fresh nonce on every request', function (): void {
    $first = $this->get('/')->headers->get('Content-Security-Policy');
    $second = $this->get('/')->headers->get('Content-Security-Policy');

    expect($first)->not->toBe($second);
});

it('upgrades insecure sub-resource requests over https', function (): void {
    expect($this->get('https://localhost/')->headers->get('Content-Security-Policy'))
        ->toEndWith('; upgrade-insecure-requests');
});

it('allows the Vite dev server while it is running hot', function (): void {
    $this->withVite();
    $hotFile = tempnam(sys_get_temp_dir(), 'hot');
    file_put_contents($hotFile, "http://localhost:5173\n");
    Vite::useHotFile($hotFile);

    $policy = $this->get('/')->headers->get('Content-Security-Policy');

    expect($policy)
        ->toContain("script-src 'self' http://localhost:5173 'nonce-")
        ->toContain("style-src 'self' http://localhost:5173 'nonce-")
        ->toContain("font-src 'self' http://localhost:5173")
        ->toContain("connect-src 'self' http://localhost:5173 ws://localhost:5173");

    File::delete($hotFile);
});

it('sends a baseline policy to the admin panel, which needs inline Alpine scripts', function (string $url): void {
    actingAsAdmin();

    $this->get($url)
        ->assertHeader('Content-Security-Policy', "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'");
})->with(['/admin', '/admin/login']);

it('sends the baseline policy to the health check page', function (): void {
    $this->get('/up')
        ->assertOk()
        ->assertHeader('Content-Security-Policy', "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'");
});
