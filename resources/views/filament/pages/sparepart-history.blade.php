<x-filament-panels::page>

    {{-- FILTER --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="text-xs text-slate-400 mb-1 block">Bulan</label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="month">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
        <div>
            <label class="text-xs text-slate-400 mb-1 block">Tahun</label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="year">
                    @foreach (range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        {{-- RINGKASAN --}}
        @php
            $totalMasuk = collect($historyData)
                ->whereIn('tipe', ['MASUK', 'RETUR'])
                ->sum('jumlah');
            $totalKeluar = collect($historyData)
                ->whereIn('tipe', ['PINJAM', 'PAKAI', 'DEPLOY'])
                ->sum('jumlah');
            $totalTrx = collect($historyData)->count();
        @endphp
        <div class="col-span-2 flex gap-3 items-end">
            <div
                style="flex:1;background:#064e3b22;border:1px solid #065f46;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#6ee7b7">Total Masuk</div>
                <div style="font-size:20px;font-weight:bold;color:#34d399">+{{ number_format($totalMasuk) }}</div>
            </div>
            <div
                style="flex:1;background:#4c051922;border:1px solid #9f1239;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#fca5a5">Total Keluar</div>
                <div style="font-size:20px;font-weight:bold;color:#f87171">-{{ number_format($totalKeluar) }}</div>
            </div>
            <div
                style="flex:1;background:#1e293b;border:1px solid #553a33;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#94a3b8">Total Transaksi</div>
                <div style="font-size:20px;font-weight:bold;color:#e2e8f0">{{ $totalTrx }}</div>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div style="background:#191c22;border:1px solid #334155;border-radius:12px;padding:16px">
        @if (empty($historyData) || count($historyData) === 0)
            <div style="text-align:center;padding:48px;color:#64748b">
                <p>Tidak ada data mutasi untuk periode ini.</p>
            </div>
        @else
            <table style="width:100%;border-collapse:collapse;color:#e2e8f0;font-size:13px">
                <thead>
                    <tr style="background:#0f172a;color:#94a3b8;font-size:11px;text-transform:uppercase">
                        <th style="padding:10px 12px;border-bottom:1px solid #554c33;text-align:left">Tanggal</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:left">Nama Sparepart
                        </th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:left">Tipe</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:center">Jumlah</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #553339;text-align:left">Detail Alur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historyData as $item)
                        @php
                            $badgeStyle = match ($item->tipe) {
                                'MASUK' => 'background:#064e3b;color:#6ee7b7;border:1px solid #065f46',
                                'PINJAM' => 'background:#1e3a5f;color:#93c5fd;border:1px solid #1e40af',
                                'PAKAI' => 'background:#4c0519;color:#fca5a5;border:1px solid #9f1239',
                                'DEPLOY' => 'background:#431407;color:#fdba74;border:1px solid #9a3412',
                                'ROLLING' => 'background:#1e293b;color:#94a3b8;border:1px solid #475569',
                                'RETUR' => 'background:#422006;color:#fde68a;border:1px solid #92400e',
                                default => 'background:#1e293b;color:#94a3b8;border:1px solid #475569',
                            };
                            $isKeluar = in_array($item->tipe, ['PINJAM', 'PAKAI', 'DEPLOY']);
                            $isMasuk = in_array($item->tipe, ['MASUK', 'RETUR']);
                        @endphp
                        <tr style="border-bottom:1px solid #1e293b" onmouseover="this.style.background='#0f172a'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:10px 12px;color:#dddee0">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td style="padding:10px 12px;font-weight:600">
                                @if ($item->part === '-')
                                    <span style="color:#475569;font-style:italic">—</span>
                                @else
                                    {{ $item->part }}
                                @endif
                            </td>
                            <td style="padding:10px 12px">
                                <span
                                    style="{{ $badgeStyle }};padding:3px 8px;border-radius:4px;font-size:11px;font-weight:bold">
                                    {{ $item->tipe }}
                                </span>
                            </td>
                            <td style="padding:10px 12px;text-align:center;font-weight:bold">
                                @if ($item->jumlah == 0)
                                    <span style="color:#475569">—</span>
                                @elseif($isKeluar)
                                    <span style="color:#f87171">-{{ $item->jumlah }}</span>
                                @elseif($isMasuk)
                                    <span style="color:#34d399">+{{ $item->jumlah }}</span>
                                @else
                                    <span style="color:#94a3b8">{{ $item->jumlah }}</span>
                                @endif
                            </td>
                            <td style="padding:10px 12px;color:#d2dae6;font-size:12px">{{ $item->detail }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</x-filament-panels::page>
