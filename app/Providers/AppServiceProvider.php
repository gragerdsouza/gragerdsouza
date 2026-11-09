<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Profile;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

/**
 * Application bootstrapping: strict models, dates, HTTPS, password policy, rate limits and error pages.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     *
     * @return void Nothing is returned.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict(! $this->app->isProduction());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        URL::forceHttps($this->app->isProduction());

        Password::defaults(fn (): Password => $this->app->isProduction()
            ? Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(8));

        RateLimiter::for('public', fn (Request $request): Limit => Limit::perMinute(
            config()->integer('portfolio.public.per_minute'),
        )->by((string) $request->ip()));

        // Themed error pages need the profile for the header and footer. Server errors (5xx)
        // never query the database, which may be what failed; client errors fall back too.
        ViewFactory::composer('errors::*', static fn (View $view): View => $view->with(
            'profile',
            str_starts_with($view->name(), 'errors::5')
                ? Profile::placeholder()
                : rescue(Profile::current(...), Profile::placeholder(...), report: false),
        ));

        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perMinute(
            config()->integer('portfolio.contact.per_minute'),
        )->by((string) $request->ip()));
    }
}
