<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
// ... baris kode di atas tetap sama ...
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\StockAlert;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'orange' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Cukup tulis satu-satu saja di sini, Boss:
                Widgets\AccountWidget::class,
                StockAlert::class,    // Menampilkan peringatan stok tipis
                StatsOverview::class, // Menampilkan total mesin, customer, & usage
                
                // Pastikan file widget di bawah ini sudah Anda buat/ada filenya:
                \App\Filament\Widgets\LatestDeployments::class, 
                \App\Filament\Widgets\DeploymentChart::class,
            ])
            ->middleware([
                // ... middleware tetap sama ...
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}