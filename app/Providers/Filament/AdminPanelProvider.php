<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('GIAT Express')
            ->brandLogo(asset('assets/icons/giat-express-icon.png'))
            ->brandLogoHeight('150px')
            ->favicon(asset('assets/icons/giat-express-icon.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => '
                    <link rel="manifest" href="/build/manifest.webmanifest">
                    <meta name="theme-color" content="#800000">
                    <link rel="apple-touch-icon" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="192x192" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="128x128" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="96x96" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="64x64" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/giat-express-icon.png">
                    <link rel="icon" type="image/png" href="/assets/icons/giat-express-icon.png">
                ',
            );
    }
}
