<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ranking Pemakaian DGG</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            padding: 24px;
            background: #fff;
            color: #1a1a1a;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px double #1e3a5f;
        }

        .header .company {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .header .periode {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ===== TABEL ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        thead tr {
            background: #1e3a5f;
            color: #ffffff;
        }

        th {
            padding: 8px 10px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            border: 1px solid #1e3a5f;
        }

        td {
            padding: 7px 10px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
        }

        /* ===== BARIS GANJIL/GENAP ===== */
        tbody tr:nth-child(odd) {
            background: #f8fafc;
        }

        tbody tr:nth-child(even) {
            background: #ffffff;
        }

        tbody tr:hover {
            background: #eff6ff;
        }

        /* ===== RANK ===== */
        .rank-1 {
            background: #fef3c7 !important;
            font-weight: bold;
            color: #92400e;
        }

        .rank-2 {
            background: #f1f5f9 !important;
            font-weight: bold;
            color: #475569;
        }

        .rank-3 {
            background: #fef2f2 !important;
            font-weight: bold;
            color: #b91c1c;
        }

        /* ===== KOLOM ===== */
        .col-rank {
            width: 40px;
            font-weight: bold;
        }

        .col-bw {
            color: #1d4ed8;
            font-weight: 600;
        }

        .col-color {
            color: #dc2626;
            font-weight: 600;
        }

        .col-total {
            background: #f0fdf4 !important;
            font-weight: bold;
            color: #15803d;
        }

        .col-avg {
            background: #1e3a5f !important;
            color: #ffffff !important;
            font-weight: bold;
            border-radius: 3px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        /* ===== PRINT ===== */
        @media print {
            body {
                padding: 12px;
            }

            tbody tr:hover {
                background: inherit;
            }

            .rank-1,
            .rank-2,
            .rank-3 {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead tr,
            .col-avg {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div class="company">⚙ DINAMIKA GLOBAL GEMILANG (DGG)</div>
        <div class="title">Ranking Pemakaian Customer</div>
        <div class="periode">Periode: {{ strtoupper($bulan) }} {{ $tahun }} &nbsp;|&nbsp; Dicetak:
            {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-rank">RANK</th>
                <th class="text-left">NAMA CUSTOMER</th>
                <th>SN MESIN</th>
                <th>TOTAL BW</th>
                <th>TOTAL COLOR</th>
                <th>GRAND TOTAL</th>
                <th>LAMA PASANG</th>
                <th>RATA-RATA / BULAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $row)
                @php
                    $rankClass = match ($index) {
                        0 => 'rank-1',
                        1 => 'rank-2',
                        2 => 'rank-3',
                        default => '',
                    };
                    $rankIcon = match ($index) {
                        0 => '🥇',
                        1 => '🥈',
                        2 => '🥉',
                        default => $index + 1,
                    };
                @endphp
                <tr class="{{ $rankClass }}">
                    <td class="col-rank">{{ $rankIcon }}</td>
                    <td class="text-left"><strong>{{ $row->nama_customer }}</strong></td>
                    <td>{{ $row->serial_number }}</td>
                    <td class="col-bw">{{ number_format($row->total_bw) }}</td>
                    <td class="col-color">{{ number_format($row->total_color) }}</td>
                    <td class="col-total">{{ number_format($row->total_semua) }}</td>
                    <td>{{ $row->jumlah_bulan }} bln</td>
                    <td class="col-avg">{{ number_format($row->rata_rata) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>DGG System &copy; {{ date('Y') }} &mdash; Developer RUDIANTO</span>
        <span>Total Customer: {{ count($records) }}</span>
    </div>

</body>

</html>
