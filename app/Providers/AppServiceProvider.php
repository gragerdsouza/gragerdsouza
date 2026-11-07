<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Application bootstrapping: strict models, dates, HTTPS and rate limits.
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

        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perMinute(
            config()->integer('portfolio.contact.per_minute'),
        )->by((string) $request->ip()));
    }
}
