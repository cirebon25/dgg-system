<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Stock Mesin - PT. DGG</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 13px;
            color: #000;
            background: #fff;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* ===== HEADER (dipakai di 2 halaman, teks atas selalu bold) ===== */
        header {
            text-align: center;
            margin-bottom: 8px;
        }

        header h1,
        header h2,
        header h3,
        header p {
            font-weight: 700;
            text-transform: uppercase;
        }

        header h1 {
            font-size: 15px;
        }

        header h2 {
            font-size: 15px;
        }

        header h3 {
            font-size: 13px;
            margin-top: 2px;
        }

        header p {
            font-size: 12px;
            margin-top: 2px;
        }

        /* ===== TABEL UMUM ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 17px;
            text-align: center;
        }

        td.text-left {
            text-align: left;
        }

        /* ===== HALAMAN 1: GAYA EXCEL ===== */
        col.h1-no {
            width: 24px;
        }

        col.h1-tipe {
            width: 32%;
            /* Dikecilkan dari 34% ke 28% */
        }

        col.h1-jumlah {
            width: 48px;
        }

        col.h1-status {
            width: 80px;
        }

        tr.row-highlight td {
            background: #fef9c3 !important;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* tr.row-highlight td {
            font-weight: 700;
        } */

        tr.row-inventaris td {
            background: #fef9c3 !important;
            font-weight: 700;
        }

        tr.row-total td {
            font-weight: 700;
            font-style: italic;
            font-size: 13px;
        }

        /* ===== HALAMAN 2: DETAIL + SERIAL NUMBER ===== */
        col.h2-no {
            width: 24px;
        }

        col.h2-tipe {
            width: 16%;
        }

        col.h2-volt {
            width: 34px;
        }

        col.h2-total {
            width: 55px;
        }

        col.h2-kaset {
            width: 34px;
        }

        col.h2-finish {
            width: 34px;
        }

        col.h2-dscan {
            width: 34px;
        }

        col.h2-status {
            width: 70px;
        }

        th.bg-total-header {
            background: #ffea31 !important;
            color: #000;
        }

        .bg-total-data {
            background: #fffde7 !important;
            color: #b45309;
            font-weight: 700;
        }

        .col-tipe-continued {
            color: #94a3b8;
            font-style: italic;
        }

        .row-new-type td {
            border-top: 2px solid #94a3b8;
        }

        .badge-status {
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 8.5px;
            display: inline-block;
        }

        .badge-ready {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-perbaikan {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-inventaris {
            background: #fde047;
            color: #713f12;
        }

        .badge-ex-luar {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-other {
            background: #f1f5f9;
            color: #334155;
        }

        .sn-text {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            font-weight: 600;
        }

        @media print {
            tr {
                page-break-inside: avoid;
            }

            th.bg-total-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-total-data {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr.row-inventaris td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
@php
    // Mapping label tampilan: status di DB tetap "Ready",
    // tapi yang tercetak di kertas jadi "EX LUAR".
    // Highlight/badge warna TETAP mengikuti status asli, bukan label tampilan.
    $statusLabelMap = [
        'ready' => 'EX LUAR',
    ];
@endphp

<body>

    {{-- ============================== --}}
    {{-- HALAMAN 1 — RINGKASAN GAYA EXCEL --}}
    {{-- ============================== --}}
    <div class="page">

        <header>
            <h1>STOCK MESIN MESIN PHOTO COPY</h1>
            <h2>PT. DINAMIKA GLOBAL GEMILANG</h2>
            <h3>DEPO {{ strtoupper($depo ?? 'CIREBON') }}</h3>
            <p>PERTANGGAL {{ strtoupper(\Carbon\Carbon::parse($tanggal ?? now())->translatedFormat('d F Y')) }}</p>
        </header>

        <table>
            <colgroup>
                <col class="h1-no">
                <col class="h1-tipe">
                <col class="h1-jumlah">
                <col>
                <col>
                <col>
                <col class="h1-status">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2" class="text-left">TYPE MESIN</th>
                    <th rowspan="2">JUMLAH<br>MESIN</th>
                    <th colspan="4">KETERANGAN</th>
                </tr>
                <tr>
                    <th>4 KASET</th>
                    <th>FINISHER</th>
                    <th>DOUBLE SCAN</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @php $totalUnitP1 = 0; @endphp

                @foreach ($stocks as $s)
                    @php
                        $statusDisplay = strtoupper($s->asal_mesin ?: $s->status);
                        $isHighlight = strtolower($s->asal_mesin) === 'kanibal';
                        $totalUnitP1 += $s->total_unit;

                        // Kolom kaset: tampilkan jumlah unit yang punya kaset 4, kosongkan jika 0
                        $kasetDisplay = $s->kaset_4_count > 0 ? $s->kaset_4_count : '';
                    @endphp

                    <tr class="{{ $isHighlight ? 'row-highlight' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ $s->tipe_model }} - {{ $s->volt }}V</td>
                        <td>{{ $s->total_unit }}</td>
                        <td>{{ $kasetDisplay }}</td>
                        <td>{{ $s->finisher ?: '' }}</td>
                        <td>{{ $s->double_scan ?: '' }}</td>
                        <td>{{ $statusDisplay }}</td>
                    </tr>
                @endforeach
                <tr class="row-total">
                    <td colspan="2">TOTAL</td>
                    <td>{{ $totalUnitP1 }}</td>
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 24px;">
            {{ $depo ?? 'Cirebon' }}, {{ \Carbon\Carbon::parse($tanggal ?? now())->translatedFormat('d F Y') }}
        </div>

        <table style="width:100%; border:none; margin-top:16px;">
            <tr>
                <td style="border:none; text-align:center; font-weight:700; width:50%;">Dibuat Oleh,</td>
                <td style="border:none; text-align:center; font-weight:700; width:50%;">Diketahui Oleh,</td>
            </tr>
            <tr>
                <td style="border:none; height:55px;"></td>
                <td style="border:none; height:55px;"></td>
            </tr>
            <tr>
                <td
                    style="border:none; text-align:center; font-weight:700; font-style:italic; text-decoration:underline;">
                    ( {{ $dibuatOleh ?? '................' }} )
                </td>
                <td
                    style="border:none; text-align:center; font-weight:700; font-style:italic; text-decoration:underline;">
                    ( {{ $diketahuiOleh ?? '................' }} )
                </td>
            </tr>
        </table>

    </div>

    {{-- ============================== --}}
    {{-- HALAMAN 2 — DETAIL + SERIAL NUMBER --}}
    {{-- ============================== --}}
    <div class="page">

        <header>
            <h1>PT. DINAMIKA GLOBAL GEMILANG</h1>
            <h2>REKAPITULASI STOK UNIT GUDANG</h2>
            <p>Posisi Stok: {{ \Carbon\Carbon::parse($tanggal ?? now())->translatedFormat('d F Y H:i') }} WIB</p>
        </header>

        <table>
            <colgroup>
                <col class="h2-no">
                <col class="h2-tipe">
                <col class="h2-volt">
                <col class="h2-total">
                <col class="h2-kaset">
                <col class="h2-finish">
                <col class="h2-dscan">
                <col class="h2-status">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>NO</th>
                    <th class="text-left">TIPE MESIN</th>
                    <th>VOLT</th>
                    <th class="bg-total-header">TOTAL UNIT</th>
                    <th>KASET</th>
                    <th>FINISHER</th>
                    <th>D. SCAN</th>
                    <th>STATUS</th>
                    <th class="text-left">LIST SN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $prevTipe = null;
                    $rowNum = 0;
                @endphp

                @foreach ($stocks as $s)
                    @php
                        $isNewType = $s->tipe_model !== $prevTipe;
                        $statusLowerP2 = strtolower($s->status);
                        $badgeClass = match ($statusLowerP2) {
                            'ready' => 'badge-ready',
                            'perbaikan' => 'badge-perbaikan',
                            'inventaris' => 'badge-inventaris',
                            'ex luar' => 'badge-ex-luar',
                            default => 'badge-other',
                        };
                        $rowNum++;
                    @endphp

                    <tr class="{{ $isNewType && $rowNum > 1 ? 'row-new-type' : '' }}">
                        <td>{{ $isNewType ? $no++ : '' }}</td>

                        <td class="text-left {{ $isNewType ? '' : 'col-tipe-continued' }}">
                            @if ($isNewType)
                                {{ $s->tipe_model }}
                            @else
                                &nbsp;&nbsp;&#8627; {{ $s->tipe_model }}
                            @endif
                        </td>

                        <td>{{ $s->volt ?: '-' }}V</td>
                        <td class="bg-total-data">{{ $s->total_unit }} UNIT</td>
                        <td>{{ $s->kaset ?: 0 }}</td>
                        <td>{{ $s->finisher ?: 0 }}</td>
                        <td>{{ $s->double_scan ?: 0 }}</td>
                        <td>
                            <span class="badge-status {{ $badgeClass }}">
                                {{ strtoupper($s->status) }}
                            </span>
                        </td>
                        <td class="text-left">
                            <span class="sn-text">{{ $s->list_sn }}</span>
                        </td>
                    </tr>

                    @php $prevTipe = $s->tipe_model; @endphp
                @endforeach
            </tbody>
        </table>

        <div style="text-align:right; margin-top:14px; font-weight:700;">
            GRAND TOTAL SEMUA UNIT: {{ $stocks->sum('total_unit') }} UNIT
        </div>

    </div>

</body>

</html>
