<table style="border-collapse: collapse; width: 100%;">
    {{-- ===== HEADER JUDUL ===== --}}
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold; font-size:14px; border:none;">
            STOCK MESIN MESIN PHOTO COPY
        </td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold; font-size:13px; border:none;">
            PT. DINAMIKA GLOBAL GEMILANG
        </td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold; border:none;">
            DEPO {{ strtoupper($depo) }}
        </td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold; border:none;">
            PERTANGGAL {{ strtoupper(\Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y')) }}
        </td>
    </tr>
    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>

    {{-- ===== HEADER TABEL ===== --}}
    <tr>
        <th rowspan="2" style="border:1px solid #000; background:#f1f5f9; text-align:center;">NO</th>
        <th rowspan="2" style="border:1px solid #000; background:#f1f5f9; text-align:center;">TYPE MESIN</th>
        <th rowspan="2" style="border:1px solid #000; background:#f1f5f9; text-align:center;">JUMLAH MESIN</th>
        <th colspan="4" style="border:1px solid #000; background:#f1f5f9; text-align:center;">KETERANGAN</th>
    </tr>
    <tr>
        <th style="border:1px solid #000; background:#f1f5f9; text-align:center;">4 KASET</th>
        <th style="border:1px solid #000; background:#f1f5f9; text-align:center;">FINISHER</th>
        <th style="border:1px solid #000; background:#f1f5f9; text-align:center;">DOUBLE SCAN</th>
        <th style="border:1px solid #000; background:#f1f5f9; text-align:center;">STATUS</th>
    </tr>

    {{-- ===== DATA ===== --}}
    @php $totalUnit = 0; @endphp
    @foreach ($stocks as $i => $s)
        @php
            $isHighlight = strtolower($s->asal_mesin) === 'kanibal';
            $totalUnit += $s->total_unit;
            $bg = $isHighlight ? 'background:#fef9c3; font-weight:bold;' : '';
        @endphp
        <tr>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $i + 1 }}</td>
            <td style="border:1px solid #000; text-align:left; {{ $bg }}">{{ $s->tipe_model }} -
                {{ $s->volt }}V</td>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $s->total_unit }}</td>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $s->kaset_4_count ?: '' }}
            </td>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $s->finisher ?: '' }}</td>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $s->double_scan ?: '' }}</td>
            <td style="border:1px solid #000; text-align:center; {{ $bg }}">
                {{ strtoupper($s->asal_mesin ?: $s->status) }}</td>
        </tr>
    @endforeach

    {{-- ===== TOTAL ===== --}}
    <tr>
        <td colspan="2" style="border:1px solid #000; font-weight:bold; font-style:italic;">TOTAL</td>
        <td style="border:1px solid #000; font-weight:bold; font-style:italic; text-align:center;">{{ $totalUnit }}
        </td>
        <td colspan="4" style="border:1px solid #000;"></td>
    </tr>

    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>

    {{-- ===== TANDA TANGAN ===== --}}
    <tr>
        <td colspan="7" style="border:none;">
            {{ $depo }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>
    <tr>
        <td colspan="3" style="border:none; text-align:center; font-weight:bold;">Dibuat Oleh,</td>
        <td colspan="1" style="border:none;"></td>
        <td colspan="3" style="border:none; text-align:center; font-weight:bold;">Diketahui Oleh,</td>
    </tr>
    <tr>
        <td colspan="7" style="border:none; height:50px;"></td>
    </tr>
    <tr>
        <td colspan="3" style="border:none; text-align:center; font-weight:bold; font-style:italic;">(
            .................. )</td>
        <td colspan="1" style="border:none;"></td>
        <td colspan="3" style="border:none; text-align:center; font-weight:bold; font-style:italic;">(
            .................. )</td>
    </tr>

    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border:none;"></td>
    </tr>

    {{-- ============================== --}}
    {{-- TABEL DISPENSER RO --}}
    {{-- ============================== --}}
    <tr>
        <td colspan="4" style="text-align:center; font-weight:bold; font-size:14px; border:none;">
            STOCK MESIN MESIN DISPENSER RO
        </td>
    </tr>
    <tr>
        <td colspan="4" style="text-align:center; font-weight:bold; border:none;">
            PT. DINAMIKA GLOBAL GEMILANG
        </td>
    </tr>
    <tr>
        <td colspan="4" style="text-align:center; font-weight:bold; border:none;">
            DEPO {{ strtoupper($depo) }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border:none;"></td>
    </tr>

    <tr>
        <th style="border:1px solid #000; background:#f1f5f9;">NO</th>
        <th style="border:1px solid #000; background:#f1f5f9;">TYPE MESIN</th>
        <th style="border:1px solid #000; background:#f1f5f9;">JUMLAH MESIN</th>
        <th style="border:1px solid #000; background:#f1f5f9;">KETERANGAN</th>
    </tr>

    @php
        $groupedRo = $machineAirRos->groupBy('tipe_mesin')->map(function ($items) {
            return $items
                ->groupBy(fn($item) => trim((string) $item->keterangan) !== '' ? trim($item->keterangan) : 'Baru')
                ->sortBy(fn($g, $key) => $key === 'Baru' ? 0 : 1);
        });
        $totalRo = 0;
        $mainNo = 0;
    @endphp

    @forelse ($groupedRo as $tipeMesin => $ketGroups)
        @php $mainNo++; @endphp
        @foreach ($ketGroups as $keterangan => $items)
            @php
                $isSub = !$loop->first;
                $label = $isSub ? $mainNo . chr(96 + $loop->index) : $mainNo;
                $jumlah = $items->count();
                $totalRo += $jumlah;
                $isHi = str_contains(strtolower($keterangan), 'inventaris');
                $bg = $isHi ? 'background:#fef9c3; font-weight:bold;' : '';
            @endphp
            <tr>
                <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $label }}</td>
                <td style="border:1px solid #000; text-align:left; {{ $bg }}">{{ $tipeMesin }}</td>
                <td style="border:1px solid #000; text-align:center; {{ $bg }}">{{ $jumlah }}</td>
                <td style="border:1px solid #000; text-align:left; font-style:italic; {{ $bg }}">
                    {{ $keterangan }}</td>
            </tr>
        @endforeach
    @empty
        <tr>
            <td colspan="4" style="border:1px solid #000; text-align:center; font-style:italic;">
                Tidak ada stok mesin Dispenser RO di gudang
            </td>
        </tr>
    @endforelse

    @if ($machineAirRos->count() > 0)
        <tr>
            <td colspan="2" style="border:1px solid #000; font-weight:bold; font-style:italic;">TOTAL</td>
            <td style="border:1px solid #000; font-weight:bold; font-style:italic; text-align:center;">
                {{ $totalRo }}</td>
            <td style="border:1px solid #000;"></td>
        </tr>
    @endif
</table>
