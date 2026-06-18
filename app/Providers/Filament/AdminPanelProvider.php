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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('DGG System')
            ->globalSearchKeyBindings(['command+k', 'ctrl+1'])
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'secondary' => Color::Gray,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
                'info' => Color::Cyan,
                'blue' => Color::Blue,
                'indigo' => Color::Indigo,
                'violet' => Color::Violet,
                'purple' => Color::Purple,
                'fuchsia' => Color::Fuchsia,
                'pink' => Color::Pink,
                'teal' => Color::Teal,
                'lime' => Color::Lime,
                'yellow' => Color::Yellow,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Resources\Widgets\DeploymentChart::class,
                \App\Filament\Resources\Widgets\StatsOverview::class,
                \App\Filament\Resources\Widgets\StockAlert::class,
                \App\Filament\Resources\Widgets\RecentServiceLogs::class,
                \App\Filament\Resources\Widgets\LowStockParts::class,
                \App\Filament\Resources\Widgets\TopSpareparts::class,
                \App\Filament\Resources\Widgets\LatestCustomers::class,
                \App\Filament\Resources\Widgets\MachineLocationStats::class,
                \App\Filament\Resources\Widgets\MachineRayonStats::class,
                 \App\Filament\Resources\Widgets\LatestDeployments::class,
            ])
            ->databaseNotifications()
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->navigationGroups([
                'Pusat Cetak',
                'Transaksi',
                'Master Data',
                'Gudang & Stok',
                'Sistem Arsip',
                'Laporan',
                'Pemakaian sparepart',
                'MRC & Billing',
                'Keuangan',
                'Bantuan',
            ])
       ->renderHook(
       \Filament\View\PanelsRenderHook::SIDEBAR_FOOTER,
       fn () => new \Illuminate\Support\HtmlString('
       <div
           class="flex items-center justify-center gap-x-2 px-6 py-3 border-t border-gray-100 dark:border-white/5 select-none pointer-events-none">
           <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
           <span class="text-[10px] tracking-wider font-medium uppercase text-gray-400 dark:text-gray-500 font-mono">
               Developer RUDIANTO
           </span>
       </div>
       ')
       );
    

       }

}