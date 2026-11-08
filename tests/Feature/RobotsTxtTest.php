<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / RobotsTxtTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
it('lets crawlers index production and points them to the sitemap and llms.txt', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertContent(implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: '.route('sitemap'),
            '# AI-readable summary: '.route('llms'),
            '',
        ]));
});

it('keeps every non-production environment out of search engines', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertContent("User-agent: *\nDisallow: /\n");
});

it('never reveals the admin path in robots.txt', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    expect($this->get('/robots.txt')->getContent())->not->toContain('admin');
});

it('tells search engines not to index the admin panel', function (string $url): void {
    $this->get($url)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
})->with(['/admin/login', '/admin']);

it('lets search engines index public pages', function (): void {
    $this->get('/')->assertHeaderMissing('X-Robots-Tag');
});
