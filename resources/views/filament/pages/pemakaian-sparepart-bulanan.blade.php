<x-filament-panels::page>

    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="w-48">
            {{ $this->form }}
        </div>

        <button wire:click="cetak" type="button"
            class="fi-btn relative inline-flex items-center justify-center rounded-lg font-semibold outline-none transition duration-75 focus-visible:ring-2 px-4 py-2 text-sm bg-success-600 text-white hover:bg-success-500">
            🖨️ Cetak Laporan
        </button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold sticky left-0 bg-gray-50 dark:bg-gray-800 z-10">
                        Sparepart</th>
                    @foreach ($bulanList as $num => $label)
                        <th class="px-2 py-2 text-center font-semibold">{{ $label }}</th>
                    @endforeach
                    <th class="px-3 py-2 text-center font-semibold bg-amber-50 dark:bg-amber-900/30">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matrix as $row)
                    <tr class="border-t border-gray-100 dark:border-white/5">
                        <td class="px-3 py-1.5 sticky left-0 bg-white dark:bg-gray-900 font-medium">
                            {{ $row['sparepart']->nama_sparepart }}
                        </td>
                        @foreach ($bulanList as $num => $label)
                            @php $val = $row['bulanan'][$num]; @endphp
                            <td
                                class="px-2 py-1.5 text-center {{ $val > 0 ? 'font-semibold text-rose-600 dark:text-rose-400' : 'text-gray-400' }}">
                                {{ $val === null ? '-' : $val }}
                            </td>
                        @endforeach
                        <td class="px-3 py-1.5 text-center font-bold bg-amber-50 dark:bg-amber-900/20">
                            {{ $row['total'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="px-3 py-8 text-center text-gray-400">
                            Belum ada data snapshot untuk tahun ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-white/5 border-t-2 border-gray-300 dark:border-white/10">
                <tr>
                    <td class="px-3 py-2 font-bold sticky left-0 bg-gray-50 dark:bg-gray-800">TOTAL SEMUA PART</td>
                    @foreach ($bulanList as $num => $label)
                        <td class="px-2 py-2 text-center font-bold">{{ $totalPerBulan[$num] }}</td>
                    @endforeach
                    <td class="px-3 py-2 text-center font-bold bg-amber-100 dark:bg-amber-900/40">
                        {{ array_sum($totalPerBulan) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</x-filament-panels::page>
