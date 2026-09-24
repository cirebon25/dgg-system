@push('styles')
    <style>
        /* Animasi Masuk Game Intro */
        @keyframes epicIntro {
            0% {
                opacity: 0;
                transform: scale(0.85) translateY(40px);
            }

            70% {
                transform: scale(1.02) translateY(-5px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Efek RGB Neon Glow Berputar/Berdenyut */
        @keyframes rgbGlow {
            0% {
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.4), inset 0 0 15px rgba(245, 158, 11, 0.2);
                border-color: rgba(245, 158, 11, 0.6);
            }

            33% {
                box-shadow: 0 0 25px rgba(59, 130, 246, 0.5), inset 0 0 15px rgba(59, 130, 246, 0.2);
                border-color: rgba(59, 130, 246, 0.6);
            }

            66% {
                box-shadow: 0 0 25px rgba(168, 85, 247, 0.5), inset 0 0 15px rgba(168, 85, 247, 0.2);
                border-color: rgba(168, 85, 247, 0.6);
            }

            100% {
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.4), inset 0 0 15px rgba(245, 158, 11, 0.2);
                border-color: rgba(245, 158, 11, 0.6);
            }
        }

        /* Efek Garis Laser Cyber Scanning */
        @keyframes laserScan {
            0% {
                transform: translateY(-100%);
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(1200%);
                opacity: 0.2;
            }
        }

        /* Animasi Watermark Berdenyut Halus */
        @keyframes watermarkPulse {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 0.9;
                text-shadow: 0 0 8px rgba(59, 130, 246, 0.8);
            }
        }

        .gaming-card {
            animation: epicIntro 0.9s cubic-bezier(0.19, 1, 0.22, 1) forwards,
                rgbGlow 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            border-width: 2px;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%) !important;
            backdrop-filter: blur(16px);
        }

        /* Garis Laser Berjalan */
        .gaming-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, #f59e0b, #ec4899, transparent);
            animation: laserScan 3.5s linear infinite;
            pointer-events: none;
        }

        .dev-watermark {
            animation: watermarkPulse 3s ease-in-out infinite;
        }
    </style>
@endpush

<x-filament-panels::page.simple>
    <!-- Container Utama Tema Gaming -->
    <div class="gaming-card p-6 sm:p-8 shadow-2xl">

        @if (filament()->hasRegistration())
            <x-slot name="subheading">
                {{ __('filament-panels::pages/auth/login.actions.register.before') }}
                {{ $this->registerAction }}
            </x-slot>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <x-filament-panels::form id="form" wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
        </x-filament-panels::form>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

        <!-- Watermark Developer Rudi di Bawah Form -->
        <div class="mt-6 pt-4 border-t border-gray-800/80 text-center select-none">
            <span class="dev-watermark text-[10px] font-mono tracking-widest text-cyan-400 uppercase">
                DEVELOPER RUDI // DGG SYSTEM
            </span>
        </div>

    </div>
</x-filament-panels::page.simple>
