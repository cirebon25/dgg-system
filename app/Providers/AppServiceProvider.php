<?php

namespace App\Providers;

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

    public function boot(): void
    {
        // Observer yang sudah ada — jangan dihapus
        \App\Models\MachineReplacement::observe(\App\Observers\ReplacementObserver::class);

        // Watermark sidebar Filament
        Filament::renderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn(): HtmlString => new HtmlString('
        <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700">
            <p class="text-[9px] text-center text-gray-400 dark:text-gray-600 leading-snug">
                  © ' . date('Y') . ' Developer RUDIANTO 
            </p>
        </div>
    ')
        );

        // Tambahan: Menghilangkan panah spinner pada input angka secara global
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): HtmlString => new HtmlString('
                <style>
                    /* Menghilangkan panah di Chrome, Safari, Edge, Opera */
                    input::-webkit-outer-spin-button,
                    input::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }

                    /* Menghilangkan panah di Firefox */
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            '),
        );
    }
}
