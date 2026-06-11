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

        /* ── HEADER MODEREN DGG ── */
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
            letter-spacing: 0.5px;
        }

        header p {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── TABEL ELEGAN & SINKRON ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            box-shadow: 0 0 0 1px #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px 8px;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #cbd5e1;
        }

        /* Header Utama Charcoal Premium */
        th {
            background: #1e293b;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.5px;
        }

        /* WARNA KHAS TOTAL UNIT (Kuning Terang DGG Standar) */
        th.bg-total-header {
            background: #ffea31 !important;
            color: #000000 !important;
            font-weight: 700;
        }

        /* Baris Selang-Seling */
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        /* Highlight Data Total Unit (Soft Yellow di Data Body) */
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

        /* Badge Status Ready Hijau Bulat Clean */
        .badge-status {
            background: #dcfce7;
            color: #15803d;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 9.5px;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* Teks Serial Number */
        .sn-text {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 600;
            color: #0f172a;
        }

        .ket-text {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
            display: block;
        }

        /* ── GRAND TOTAL AREA ── */
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

            tbody tr:nth-child(even) {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-total-data {
                background: #fffde7 !important;
                color: #b45309 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-status {
                background: #dcfce7 !important;
                color: #15803d !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-box {
                background: #f1f5f9 !important;
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
            <col style="width: 40px;">
            <col style="width: 170px;">
            <col style="width: 65px;">
            <col style="width: 100px;">
            <col style="width: 65px;">
            <col style="width: 65px;">
            <col style="width: 65px;">
            <col style="width: 90px;">
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
                <th class="text-left">LIST SN / KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $index => $s)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left font-bold" style="color: #0f172a;">{{ $s->tipe_model }}</td>
                    <td><span class="font-bold">{{ $s->volt ?: '-' }}V</span></td>
                    <td class="bg-total-data">{{ $s->total_unit }} UNIT</td>
                    <td>{{ $s->kaset ?: 0 }}</td>
                    <td>{{ $s->finisher ?: 0 }}</td>
                    <td>{{ $s->double_scan ?: 0 }}</td>
                    <td><span class="badge-status">READY</span></td>
                    <td class="text-left" style="line-height: 1.4;">
                        <span class="sn-text">SN: {{ $s->list_sn }}</span>
                        @if ($s->info)
                            <span class="ket-text">Ket: {{ $s->info }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-container">
        <div class="total-box">
            GRAND TOTAL STOK READY: <strong>{{ $stocks->sum('total_unit') }} UNIT</strong>
        </div>
    </div>

</body>

</html>
