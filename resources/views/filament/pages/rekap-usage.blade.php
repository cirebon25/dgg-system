<x-filament-panels::page>

    {{-- Ambil data sekali saja untuk efisiensi --}}
    @php
        $data = $this->getUsageData();
    @endphp

    {{-- Filter Bulan & Tahun --}}
    <div
        class="flex flex-wrap items-end gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
            <select wire:model.live="month"
                class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:ring-amber-500 focus:border-amber-500">
                @foreach ([
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ] as $val => $label)
                    <option value="{{ $val }}" @selected($month == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
            <select wire:model.live="year"
                class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:ring-amber-500 focus:border-amber-500">
                @foreach (range(date('Y'), 2023) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="text-xs text-gray-400 pb-1">
            Menampilkan data periode
            <b class="text-amber-600">{{ $this->getNamaBulan($month) }} {{ $year }}</b>
        </div>
    </div>

    {{-- TOP 3 BW & Color Summary Cards --}}
    @if ($data->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- TOP 3 BW --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    🏆 Top 3 Pemakaian BW Terbanyak
                </h3>
                @foreach ($data->sortByDesc('total_bw')->take(3) as $rank => $row)
                    @php
                        $medal = match ($rank) {
                            0 => '🥇',
                            1 => '🥈',
                            2 => '🥉',
                            default => '',
                        };
                        $bg = match ($rank) {
                            0 => 'bg-amber-50 border-amber-300 dark:bg-amber-950/20 dark:border-amber-700',
                            1 => 'bg-gray-50 border-gray-300 dark:bg-gray-900 dark:border-gray-600',
                            2 => 'bg-orange-50 border-orange-200 dark:bg-orange-950/20 dark:border-orange-700',
                            default => '',
                        };
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-xl border {{ $bg }} mb-2">
                        <div>
                            <span class="text-sm">{{ $medal }}</span>
                            <span
                                class="text-xs font-bold text-gray-800 dark:text-gray-200 ml-1">{{ $row->nama_customer }}</span>
                            <div class="text-[10px] text-gray-500 ml-5">{{ $row->serial_number }} ·
                                {{ $row->tipe_model }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-gray-900 dark:text-white">
                                {{ number_format($row->total_bw) }}</div>
                            <div class="text-[10px] text-gray-400">lembar BW</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOP 3 Color --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    🎨 Top 3 Pemakaian Color Terbanyak
                </h3>
                @foreach ($data->sortByDesc('total_color')->take(3) as $rank => $row)
                    @php
                        $medal = match ($rank) {
                            0 => '🥇',
                            1 => '🥈',
                            2 => '🥉',
                            default => '',
                        };
                        $bg = match ($rank) {
                            0 => 'bg-blue-50 border-blue-300 dark:bg-blue-950/20 dark:border-blue-700',
                            1 => 'bg-gray-50 border-gray-300 dark:bg-gray-900 dark:border-gray-600',
                            2 => 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/20 dark:border-indigo-700',
                            default => '',
                        };
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-xl border {{ $bg }} mb-2">
                        <div>
                            <span class="text-sm">{{ $medal }}</span>
                            <span
                                class="text-xs font-bold text-gray-800 dark:text-gray-200 ml-1">{{ $row->nama_customer }}</span>
                            <div class="text-[10px] text-gray-500 ml-5">{{ $row->serial_number }} ·
                                {{ $row->tipe_model }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-blue-600 dark:text-blue-400">
                                {{ number_format($row->total_color) }}</div>
                            <div class="text-[10px] text-gray-400">lembar Color</div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endif

    {{-- Tabel Lengkap --}}
    <div id="print-area"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Header Cetak --}}
        <div class="print-header px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div class="text-center space-y-0.5">
                <h1 class="text-base font-black text-gray-900 dark:text-white tracking-wide uppercase">
                    Laporan Ranking Pemakaian Mesin
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    PT Dinamika Global Gemilang — DGG System
                </p>
                <p class="text-xs font-semibold text-amber-600">
                    Periode: {{ $this->getNamaBulan($month) }} {{ $year }}
                </p>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-900 dark:bg-gray-950 text-white">
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700" rowspan="2">No.</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700" rowspan="2">Customer</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700" rowspan="2">Rayon</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700" rowspan="2">Teknisi</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700" rowspan="2">Serial Number
                        </th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700" rowspan="2">Tipe</th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700" rowspan="2">Tgl Instal
                        </th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700" rowspan="2">Lama (Bln)
                        </th>
                        {{-- Pemakaian Bulan Ini --}}
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700 bg-gray-700" colspan="3">
                            Pemakaian Bulan Ini</th>
                        {{-- Lifetime --}}
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700 bg-gray-600" colspan="3">
                            Lifetime</th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700" rowspan="2">Kunjungan
                        </th>
                    </tr>
                    <tr class="bg-gray-800 dark:bg-gray-900 text-white">
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-gray-200">BW</th>
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-blue-300">Color</th>
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-amber-300">Total</th>
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-gray-300">BW</th>
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-blue-200">Color</th>
                        <th class="px-3 py-2 text-right font-bold border border-gray-700 text-amber-200">Rata-rata/Bln
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                        @php
                            $rankBg = match ($index) {
                                0 => 'bg-amber-50 dark:bg-amber-950/30',
                                1 => 'bg-gray-50 dark:bg-gray-900',
                                2 => 'bg-orange-50 dark:bg-orange-950/20',
                                default => $index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-900',
                            };
                            $rankBadge = match ($index) {
                                0 => '🥇',
                                1 => '🥈',
                                2 => '🥉',
                                default => $index + 1,
                            };
                        @endphp
                        <tr class="{{ $rankBg }} hover:bg-amber-50 dark:hover:bg-amber-950/20 transition-colors">

                            <td
                                class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 font-bold text-sm">
                                {{ $rankBadge }}
                            </td>

                            <td
                                class="px-3 py-2 border border-gray-100 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">
                                {{ $row->nama_customer }}
                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700">
                                @php
                                    $rayon = strtoupper($row->nama_rayon);
                                    $rayonClass = match (true) {
                                        str_contains($rayon, 'UTARA') => 'bg-green-100 text-green-700',
                                        str_contains($rayon, 'SELATAN') => 'bg-red-100 text-red-700',
                                        str_contains($rayon, 'BARAT DAYA') => 'bg-yellow-100 text-yellow-700',
                                        str_contains($rayon, 'BARAT') => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $rayonClass }}">
                                    {{ $row->nama_rayon }}
                                </span>
                            </td>

                            <td
                                class="px-3 py-2 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $row->nama_technician ?? '-' }}
                            </td>

                            <td
                                class="px-3 py-2 border border-gray-100 dark:border-gray-700 font-mono text-gray-700 dark:text-gray-300">
                                {{ $row->serial_number }}
                            </td>

                            <td
                                class="px-3 py-2 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $row->tipe_model }}
                            </td>

                            <td
                                class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($row->tanggal_instal)->format('d/m/Y') }}
                            </td>

                            <td
                                class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $row->lama_pasang }}
                            </td>

                            {{-- Pemakaian Bulan Ini --}}
                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-semibold text-gray-800 dark:text-gray-200">
                                {{ number_format($row->total_bw) }}
                            </td>

                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($row->total_color) }}
                            </td>

                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-bold
                                {{ $index === 0 ? 'text-white bg-amber-500' : 'text-amber-600' }}">
                                {{ number_format($row->total_bulan) }}
                            </td>

                            {{-- Lifetime --}}
                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono text-gray-500 dark:text-gray-400 text-[10px]">
                                {{ number_format($row->total_bw_life ?? $row->total_hidup) }}
                            </td>

                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono text-blue-400 dark:text-blue-300 text-[10px]">
                                {{ number_format($row->total_color_life ?? 0) }}
                            </td>

                            <td
                                class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono text-gray-500 dark:text-gray-400 text-[10px]">
                                {{ number_format($row->rata_rata) }}
                            </td>

                            <td
                                class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $row->total_kunjungan }}x
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-6 py-10 text-center text-gray-400 text-sm">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($data->count() > 0)
                    <tfoot>
                        <tr class="bg-amber-50 dark:bg-amber-950/30 font-bold border-t-2 border-amber-300">
                            <td colspan="8"
                                class="px-3 py-2.5 text-right text-xs border border-gray-200 dark:border-gray-700 text-gray-600">
                                TOTAL KESELURUHAN
                            </td>
                            {{-- Total BW Bulan Ini --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                                {{ number_format($data->sum('total_bw')) }}
                            </td>
                            {{-- Total Color Bulan Ini --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-blue-600">
                                {{ number_format($data->sum('total_color')) }}
                            </td>
                            {{-- Total Keseluruhan Bulan Ini --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-amber-600">
                                {{ number_format($data->sum('total_bulan')) }}
                            </td>
                            {{-- Total Lifetime BW --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-500">
                                {{ number_format($data->sum(fn($row) => $row->total_bw_life ?? $row->total_hidup)) }}
                            </td>
                            {{-- Total Lifetime Color --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-blue-400">
                                {{ number_format($data->sum(fn($row) => $row->total_color_life ?? 0)) }}
                            </td>
                            {{-- Total Rata-rata / Bulan --}}
                            <td
                                class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-500">
                                {{ number_format($data->sum('rata_rata')) }}
                            </td>
                            {{-- Total Kunjungan --}}
                            <td
                                class="px-3 py-2.5 text-center text-xs border border-gray-200 dark:border-gray-700 text-gray-600">
                                {{ number_format($data->sum('total_kunjungan')) }}x
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <p class="text-[10px] text-gray-400">Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
            <p class="text-[10px] text-gray-400">DGG System v3.0 — PT Dinamika Global Gemilang</p>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print-header {
                display: block !important;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('printPage', () => {
                window.print();
            });
        });
    </script>

</x-filament-panels::page>
