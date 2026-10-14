<?php

namespace App\Providers;

use App\Models\MachineEvent;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Registrasi Model Observers (Sistem Finansial & Inventaris)
        |--------------------------------------------------------------------------
        | PENTING: AccommodationClaimObserver SUDAH DICABUT TOTAL dari sini
        | untuk menghindari error 500 Class Not Found.
        |
        | CashMutationObserver JUGA DICABUT — Buku Kas (CashLedger) tidak lagi
        | tersinkron otomatis dari SPM (CashMutation). Admin mencatat saldo
        | secara manual langsung di CashLedgerResource.
        */
        if (class_exists(\App\Models\MachineReplacement::class) && class_exists(\App\Observers\ReplacementObserver::class)) {
            \App\Models\MachineReplacement::observe(\App\Observers\ReplacementObserver::class);
        }

        // if (class_exists(\App\Models\CashMutation::class) && class_exists(\App\Observers\CashMutationObserver::class)) {
        //     \App\Models\CashMutation::observe(\App\Observers\CashMutationObserver::class);
        // }

        /*
        |--------------------------------------------------------------------------
        | Event Mesin (riwayat per No Seri)
        |--------------------------------------------------------------------------
        | Setiap Service Log / part yang dipasang otomatis dicatat ke tabel
        | machine_events. Edit, hapus (soft delete), dan restore ikut disinkronkan.
        */
        if (class_exists(MachineEvent::class)) {
            ServiceLog::saved(fn($log) => MachineEvent::syncServiceLog($log));

            ServiceLog::deleted(fn($log) => MachineEvent::where('service_log_id', $log->id)->delete());

            // Saat Service Log dipulihkan (restore), event dibuat ulang
            ServiceLog::restored(function ($log) {
                MachineEvent::syncServiceLog($log);

                ServiceLogSparepart::where('service_log_id', $log->id)->get()
                    ->each(fn($item) => MachineEvent::syncPart($item));
            });

            ServiceLogSparepart::saved(fn($item) => MachineEvent::syncPart($item));
            ServiceLogSparepart::deleted(fn($item) => MachineEvent::where('service_log_sparepart_id', $item->id)->delete());
        }

        /*
        |--------------------------------------------------------------------------
        | Kustomisasi Antarmuka Global Filament UI
        |--------------------------------------------------------------------------
        */
        Filament::renderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn(): HtmlString => new HtmlString('
                <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                    <p class="text-[10px] text-center text-gray-400 dark:text-gray-500 leading-relaxed font-medium">
                        © ' . date('Y') . ' Developer RUDIANTO
                    </p>
                </div>
            ')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): HtmlString => new HtmlString('
                <style>
                    input::-webkit-outer-spin-button,
                    input::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            '),
        );
    }
}