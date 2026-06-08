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
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
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

            // ===== GLOBAL SEARCH (tetap aktif, shortcut dipertahankan) =====
            ->globalSearch(true)

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
                'Form SPM',
                'Bantuan',
            ])

            // ===== WATERMARK SIDEBAR =====
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn(): HtmlString => new HtmlString('
                    <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                        <p class="text-[10px] text-center text-gray-400 dark:text-gray-500 leading-relaxed font-medium">
                            © ' . date('Y') . ' Developer RUDIANTO
                        </p>
                    </div>
                ')
            )

            // // ===== KUSTOMISASI TAMPILAN GLOBAL SEARCH DI TOPBAR =====
            // ->renderHook(
            //     PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            //     fn(): HtmlString => new HtmlString('
            //         <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs pr-1">
            //             <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
            //                 viewBox="0 0 24 24" stroke="currentColor">
            //                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            //                     d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            //             </svg>
            //             <span class="hidden md:inline">Cari menu, data...</span>
            //         </div>
            //     ')
            // )
        ;
    }
}
