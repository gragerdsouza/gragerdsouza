<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\RestrictAdminAccess;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Configures the Filament admin panel (mounted at /admin unless ADMIN_PATH says otherwise).
 *
 * Hardening: optional IP allowlist (404 for everyone else), authenticator-app 2FA with
 * recovery codes (required by default), and Filament's built-in login throttling.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class AdminPanelProvider extends PanelProvider
{
    /**
     * Configure the admin panel.
     *
     * @param Panel $panel The Filament panel being configured or accessed.
     *
     * @return Panel The configured panel.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(config()->string('portfolio.admin.path'))
            ->login()
            ->passwordReset()
            ->profile()
            ->multiFactorAuthentication(
                [AppAuthentication::make()->recoverable()],
                isRequired: config()->boolean('portfolio.admin.require_mfa'),
            )
            ->brandName('Portfolio admin')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->navigationGroups(['Content', 'Inbox'])
            ->navigationItems([
                NavigationItem::make('View site')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-globe-alt')
                    ->sort(99),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            // Runs first, on page loads and Livewire updates alike.
            ->middleware([RestrictAdminAccess::class], isPersistent: true)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
