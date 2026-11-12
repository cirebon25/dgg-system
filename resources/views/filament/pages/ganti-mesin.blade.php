<x-filament-panels::page>

    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg" color="warning" icon="heroicon-o-arrow-path">
                PROSES TUKAR GULING SEKARANG
            </x-filament::button>
        </div>
    </form>

    <div class="mt-10">
        <h2 class="flex items-center gap-2 text-lg font-bold mb-4 text-[rgb(var(--dgg-text-base))]">
            <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 text-primary-500" />
            Riwayat Rolling Unit (20 Terakhir)
        </h2>

        @if (count($riwayat) === 0)
            <div
                class="p-6 bg-[rgb(var(--dgg-surface))] rounded-xl border border-[rgb(var(--dgg-border))] text-center text-[rgb(var(--dgg-text-muted))] text-sm">
                Belum ada riwayat rolling.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-[rgb(var(--dgg-border))] bg-[rgb(var(--dgg-surface))]">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-[rgb(var(--dgg-body))]/50 text-[rgb(var(--dgg-text-muted))] uppercase text-xs border-b border-[rgb(var(--dgg-border))]">
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
                    <tbody class="divide-y divide-[rgb(var(--dgg-border))]">
                        @foreach ($riwayat as $row)
                            <tr class="transition hover:bg-white/[0.02]">
                                <td class="px-4 py-3 whitespace-nowrap opacity-80">
                                    {{ filled($row['tanggal'] ?? null) ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}
                                </td>

                                <td class="px-4 py-3 font-semibold text-[rgb(var(--dgg-text-base))]">
                                    {{ $row['nama_customer'] ?? '-' }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-red-400"
                                    title="{{ $row['sn_lama'] ?? '-' }}">
                                    {{ \Illuminate\Support\Str::limit($row['sn_lama'] ?? '-', 30, '') }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-green-400"
                                    title="{{ $row['sn_baru'] ?? '-' }}">
                                    {{ \Illuminate\Support\Str::limit($row['sn_baru'] ?? '-', 30, '') }}
                                </td>

                                <td class="px-4 py-3 text-xs whitespace-nowrap">
                                    <span class="block">BW: {{ number_format($row['counter_bw_final'] ?? 0) }}</span>
                                    <span class="block text-[rgb(var(--dgg-text-muted))]">
                                        CL: {{ number_format($row['counter_color_final'] ?? 0) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $row['nama_technician'] ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-xs text-[rgb(var(--dgg-text-muted))] max-w-[14rem]">
                                    <div class="truncate" title="{{ $row['keterangan'] ?? '-' }}">
                                        {{ $row['keterangan'] ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex gap-4 justify-center text-xs font-bold">
                                        <a href="{{ route('cetak.sj-rolling', $row['id']) }}" target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center gap-1.5 text-blue-400 hover:underline">
                                            <x-filament::icon icon="heroicon-o-printer" class="h-4 w-4" />
                                            Cetak SJ
                                        </a>

                                        <a href="{{ route('filament.admin.pages.edit-machine-replacement', ['id' => $row['id']]) }}"
                                            class="inline-flex items-center gap-1.5 text-amber-400 hover:underline">
                                            <x-filament::icon icon="heroicon-o-pencil-square" class="h-4 w-4" />
                                            Edit
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
