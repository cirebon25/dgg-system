<x-filament-panels::page.simple>
    <div class="dgg-login-card p-6 sm:p-8">

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

        <div class="mt-6 border-t border-[rgb(var(--dgg-border))] pt-4 text-center select-none">
            <span class="dgg-login-watermark">
                Developer Rudi // DGG System
            </span>
        </div>

    </div>
</x-filament-panels::page.simple>
