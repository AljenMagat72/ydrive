<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CreateAccount;
use App\Http\Middleware\AdminAuthenticate;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Route;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->brandName('YDrive App')
            ->default()
            ->id('admin')

            ->login()
            ->passwordReset()
            ->profile()
            ->authGuard('admin')
            ->authPasswordBroker('admins')
            ->authMiddleware([
                AdminAuthenticate::class
            ])
            ->colors([
                'primary' => '#407dbb',
            ])
            ->viteTheme('resources/frontend/admin/app.css', 'build/admin')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->routes(function () {
                Route::get(CreateAccount::$slug, CreateAccount::class)->name(CreateAccount::$route);
            })
            ->breadcrumbs(true)
            ->userMenu(true)
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false)
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->sidebarWidth('12rem')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentJobsMonitorPlugin::make()->navigationGroup('Settings')
            ])
            ->spa();

        if (app()->environment('local')) {
            $panel->path('admin');
        } else {
            $panel->domain('admin');
        }

        return $panel;
    }
}
