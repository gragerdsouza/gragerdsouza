<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / ErrorPagesTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    Route::middleware('web')->get('/_test/abort/{code}', fn (int $code) => abort($code, 'internal reason, never shown'));
    Route::middleware('web')->get('/_test/crash', fn (): never => throw new RuntimeException('database password is hunter2'));
});

it('renders a themed page for each error status', function (int $code, string $title): void {
    config()->set('app.name', 'Grager D\'souza');

    $this->get("/_test/abort/{$code}")
        ->assertStatus($code)
        ->assertSee($title)
        ->assertSee("<title>{$title} · Grager D&#039;souza</title>", false)
        ->assertSee('~/grager-dsouza', false)
        ->assertSee("HTTP/2 {$code}")
        ->assertSee('href="'.route('home').'"', false)
        ->assertDontSee('internal reason, never shown');
})->with([
    [401, 'Sign in required'],
    [403, 'Access denied'],
    [404, 'Page not found'],
    [405, 'Method not allowed'],
    [419, 'Page expired'],
    [429, 'Too many requests'],
    [500, 'Something went wrong'],
    [503, 'Back soon'],
]);

it('falls back to a generic themed page for other statuses', function (int $code, string $title): void {
    $this->get("/_test/abort/{$code}")
        ->assertStatus($code)
        ->assertSee($title)
        ->assertSee("HTTP/2 {$code}");
})->with([
    [410, 'Gone'],
    [502, 'Bad Gateway'],
]);

it('uses the saved profile on client error pages', function (): void {
    Profile::factory()->create(['name' => 'Ada Lovelace']);

    $this->get('/no-such-page')
        ->assertNotFound()
        ->assertSee('<title>Page not found · Ada Lovelace</title>', false)
        ->assertSee('~/ada-lovelace', false);
});

it('shows the missing path on the 404 page', function (): void {
    $this->get('/no-such-page')
        ->assertNotFound()
        ->assertSee('curl -X GET /no-such-page');
});

it('shows the rejected method on the 405 page', function (): void {
    $this->delete('/')
        ->assertMethodNotAllowed()
        ->assertSee('curl -X DELETE /')
        ->assertSee('Method not allowed');
});

it('tells a throttled visitor when to try again', function (): void {
    config()->set('portfolio.public.per_minute', 1);
    $this->get('/projects')->assertOk();

    $response = $this->get('/projects')->assertTooManyRequests();

    expect($response->getContent())->toContain('Try again in '.$response->headers->get('Retry-After').' seconds.');
});

it('hides exception details on the 500 page in production', function (): void {
    config()->set('app.debug', false);

    $this->get('/_test/crash')
        ->assertInternalServerError()
        ->assertSee('Something went wrong')
        ->assertDontSee('hunter2');
});

it('renders server error pages without touching the database', function (): void {
    DB::enableQueryLog();

    $this->get('/_test/abort/503')->assertServiceUnavailable()->assertSee('Back soon');

    expect(DB::getQueryLog())->toBeEmpty();
});

it('still renders a 404 when the database is unavailable', function (): void {
    config()->set('app.name', 'My Portfolio');
    Schema::drop('profiles');

    $this->get('/no-such-page')
        ->assertNotFound()
        ->assertSee('~/my-portfolio', false);
});

describe('maintenance mode', function (): void {
    beforeEach(function (): void {
        // Keep maintenance state in this process's array cache, so parallel tests never see the site "down".
        config()->set('app.maintenance', ['driver' => 'cache', 'store' => 'array']);
    });

    afterEach(function (): void {
        $this->artisan('up');
    });

    it('shows the themed page while the site is down', function (): void {
        $this->artisan('down', ['--retry' => 60])->assertSuccessful();

        $this->get('/')
            ->assertServiceUnavailable()
            ->assertHeader('Retry-After', '60')
            ->assertSee('Back soon')
            ->assertSee('check back in about 1 minute.');
    });

    it('pre-renders the themed page with php artisan down --render', function (): void {
        $this->artisan('down', ['--render' => 'errors::503', '--retry' => 300])->assertSuccessful();

        $this->get('/projects')
            ->assertServiceUnavailable()
            ->assertSee('Back soon')
            ->assertSee('check back in about 5 minutes.')
            ->assertSee('HTTP/2 503');
    });

    it('asks for a few minutes when no retry time is given', function (): void {
        $this->artisan('down')->assertSuccessful();

        $this->get('/')->assertServiceUnavailable()->assertSee('check back in a few minutes.');
    });
});
