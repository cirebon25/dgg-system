<x-filament-widgets::widget>
    <x-filament::card class="relative">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                {{ $this->getHeading() }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.deployments.print-yearly', ['year' => $this->filter ?? date('Y')]) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    🖨️ Cetak Tahunan ({{ $this->filter ?? date('Y') }})
                </a>

                @if ($filters = $this->getFilters())
                    <x-filament::input.wrapper class="w-32">
                        <x-filament::input.select wire:model.live="filter">
                            @foreach ($filters as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                @endif
            </div>
        </div>

        <div class="relative w-full h-[300px]">
            <canvas x-data="chart({ cachedData: @js($this->getData()), type: @js($this->getType()), options: @js($this->getOptions()) })" wire:ignore></canvas>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
