<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg" color="warning" icon="heroicon-o-arrow-path">
                PROSES TUKAR GULING SEKARANG
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>