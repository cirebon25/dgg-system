<x-filament-panels::page>

    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg" color="warning" icon="heroicon-o-arrow-path">
                PROSES TUKAR GULING SEKARANG
            </x-filament::button>
        </div>
    </form>

    {{-- RIWAYAT ROLLING --}}
    <div class="mt-10">
        <h2 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-200">
            🔄 Riwayat Rolling Unit (20 Terakhir)
        </h2>

        @if (count($riwayat) === 0)
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border text-center text-gray-400 text-sm">
                Belum ada riwayat rolling.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">SN Lama</th>
                            <th class="px-4 py-3">SN Baru</th>
                            <th class="px-4 py-3">Counter Akhir</th>
                            <th class="px-4 py-3">Teknisi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        {{-- ✅ GUNAKAN ARRAY SYNTAX [] --}}
                        @foreach ($riwayat as $row)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-semibold">{{ $row['nama_customer'] ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                        {{ $row['sn_lama'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">
                                        {{ $row['sn_baru'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="block">BW: {{ number_format($row['counter_bw_final'] ?? 0) }}</span>
                                    <span class="block text-gray-500">CL:
                                        {{ number_format($row['counter_color_final'] ?? 0) }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $row['nama_technician'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">
                                    {{ $row['keterangan'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        {{-- ✅ TOMBOL CETAK SJ --}}
                                        <a href="{{ route('cetak.sj-rolling', $row['id']) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition">
                                            🖨️ Cetak SJ
                                        </a>

                                        {{-- ✅ TOMBOL EDIT --}}
                                        {{-- <a href="{{ route('filament.admin.pages.edit-machine-replacement', $row['id']) }}" --}}
                                        <a href="{{ route('filament.admin.pages.edit-machine-replacement') }}?id={{ $row['id'] }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition">
                                            ✏️ Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-filament-panels::page>
