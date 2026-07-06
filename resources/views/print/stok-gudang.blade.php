<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Stok Unit Gudang — PT. DGG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 5px;
            line-height: 1.4;
        }

        header {
            text-align: center;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        header h1 {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0f172a;
        }

        header h2 {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            margin: 4px 0;
            color: #475569;
        }

        header p {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            border: 1px solid #cbd5e1;
        }

        th,
        td {
            padding: 9px 8px;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #cbd5e1;
        }

        th {
            background: #1e293b;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.5px;
        }

        th.bg-total-header {
            background: #ffea31 !important;
            color: #000000 !important;
            font-weight: 700;
        }

        /* Baris dengan tipe_model sama tapi baris ke-2 dst (status berbeda) */
        .row-same-type td.col-tipe {
            color: #94a3b8;
            /* abu-abu, tanda ini baris lanjutan */
            font-style: italic;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        .bg-total-data {
            background: #fffde7 !important;
            color: #b45309;
            font-weight: 700;
            font-size: 12px;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: 600;
        }

        .badge-status {
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 9.5px;
            display: inline-block;
            letter-spacing: 0.3px;
        }

        .badge-ready {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-perbaikan {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-rented {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-other {
            background: #f1f5f9;
            color: #334155;
        }

        /* Garis pemisah antar tipe mesin */
        .row-new-type td {
            border-top: 2px solid #94a3b8;
        }

        .sn-text {
            font-family: 'Courier New', monospace;
            font-size: 10.5px;
            font-weight: 600;
            color: #0f172a;
        }

        .total-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .total-box {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            background: #f1f5f9;
            padding: 8px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .total-box strong {
            font-size: 14px;
            color: #0f172a;
            margin-left: 5px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            th {
                background: #1e293b !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            th.bg-total-header {
                background: #ffea31 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-total-data {
                background: #fffde7 !important;
                color: #b45309 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <header>
        <h1>PT. DINAMIKA GLOBAL GEMILANG</h1>
        <h2>REKAPITULASI STOK UNIT GUDANG</h2>
        <p>Posisi Stok: {{ date('d-m-Y H:i') }} WIB</p>
    </header>

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 160px;">
            <col style="width: 60px;">
            <col style="width: 95px;">
            <col style="width: 55px;">
            <col style="width: 60px;">
            <col style="width: 60px;">
            <col style="width: 95px;">
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
                    $statusLower = strtolower($s->status);
                    $badgeClass = match ($statusLower) {
                        'ready' => 'badge-ready',
                        'perbaikan' => 'badge-perbaikan',
                        'rented' => 'badge-rented',
                        default => 'badge-other',
                    };
                    $rowNum++;
                @endphp

                <tr class="{{ $isNewType && $rowNum > 1 ? 'row-new-type' : '' }}">
                    <td>{{ $isNewType ? $no++ : '' }}</td>

                    {{-- Kolom tipe: tampilkan nama kalau baris pertama tipe ini,
                         kalau baris lanjutan (status berbeda) tampilkan └ saja --}}
                    <td class="text-left font-bold col-tipe" style="color: {{ $isNewType ? '#0f172a' : '#94a3b8' }};">
                        @if ($isNewType)
                            {{ $s->tipe_model }}
                        @else
                            &nbsp;&nbsp;└ {{ $s->tipe_model }}
                        @endif
                    </td>

                    <td><span class="font-bold">{{ $s->volt ?: '-' }}V</span></td>
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

    <div class="total-container">
        <div class="total-box">
            GRAND TOTAL SEMUA UNIT: <strong>{{ $stocks->sum('total_unit') }} UNIT</strong>
        </div>
    </div>

</body>

</html>
