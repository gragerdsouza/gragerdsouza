<?php

declare(strict_types=1);

/**
 * Route definitions: web.php.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LlmsTxtController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\SitemapController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::middleware('throttle:public')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:contact')
        ->name('contact.store');

    // Machine-readable files: no session (crawlers would create a session row per hit)
    // and cacheable for an hour, with an ETag so repeat fetches get a 304.
    Route::withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, PreventRequestForgery::class])
        ->middleware('cache.headers:public;max_age=3600;etag')
        ->group(function (): void {
            Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
            Route::get('/llms.txt', LlmsTxtController::class)->name('llms');
            Route::get('/robots.txt', RobotsTxtController::class)->name('robots');
        });
});
