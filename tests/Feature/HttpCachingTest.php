<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / HttpCachingTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
it('serves machine-readable files without a session, so crawlers never create session rows', function (string $url): void {
    $this->get($url)
        ->assertOk()
        ->assertCookieMissing(config()->string('session.cookie'))
        ->assertCookieMissing('XSRF-TOKEN');
})->with(['/robots.txt', '/sitemap.xml', '/llms.txt']);

it('lets browsers and crawlers cache machine-readable files for an hour', function (string $url): void {
    $response = $this->get($url)->assertOk();

    expect($response->headers->get('Cache-Control'))->toContain('public')->toContain('max-age=3600')
        ->and($response->headers->get('ETag'))->toBeString()->not->toBeEmpty();
})->with(['/robots.txt', '/sitemap.xml', '/llms.txt']);

it('answers a matching If-None-Match with 304 Not Modified', function (): void {
    $etag = $this->get('/robots.txt')->headers->get('ETag');

    $this->get('/robots.txt', ['If-None-Match' => $etag])
        ->assertStatus(304)
        ->assertContent('');
});

it('still starts a session on HTML pages, which carry the CSRF-protected contact form', function (): void {
    $this->get('/')->assertCookie(config()->string('session.cookie'));
});
