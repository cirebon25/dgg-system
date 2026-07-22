<x-filament-widgets::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                {{ static::$heading }}
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

        @php
            $result = $this->getTableData();
            $reportData = $result['data'];
            $startYear = $result['startYear'];
            $endYear = $result['endYear'];
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 text-sm">
                        <th class="py-3 px-4 font-semibold">Tahun Pemasangan</th>
                        <th class="py-3 px-4 font-semibold">Total Pemasangan Baru (Unit)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10 text-sm text-gray-700 dark:text-gray-200">
                    @foreach ($reportData as $year => $count)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="py-3 px-4 font-medium">{{ $year }}</td>
                            <td class="py-3 px-4">{{ $count }} Unit</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
