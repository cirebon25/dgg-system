<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Ganti Part — Periode {{ $month }}/{{ $year }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
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
            margin: 10mm;
        }

        body {
            font-family: 'Source Sans 3', sans-serif;
            font-size: 9.5px;
            color: #111;
            background: #fff;
            padding: 10px;
        }

        /* ── KOP JUDUL UTAMA ── */
        .kop {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }

        .kop h1 {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        /* Sub-Header Periode & Rayon */
        .sub-header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .periode-box {
            text-transform: uppercase;
        }

        .rayon-box {
            background-color: #fff;
            padding: 2px 5px;
        }

        /* ── REPORT TABLE STANDARD (KUNING) ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        /* Header Kuning Terang Sesuai Lampiran */
        .report-table thead tr.h1 th,
        .report-table thead tr.h2 th {
            background: #ffea31;
            color: #000;
            border: 1px solid #000;
            padding: 5px 3px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.3;
        }

        .report-table thead tr.h1 th {
            border-top: 1px solid #000;
        }

        .report-table tbody tr {
            border-bottom: 1px solid #000;
        }

        .report-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Pembatas visual jika ganti kelompok Rayon */
        .row-rayon-divider {
            background-color: #cbd5e1 !important;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
        }

        .report-table td {
            border: 1px solid #000;
            padding: 5px 5px;
            vertical-align: middle;
            font-size: 9.5px;
            word-wrap: break-word;
        }

        .tc {
            text-align: center;
        }

        .tl {
            text-align: left;
        }

        .mono {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            font-weight: 600;
        }

        /* ── FORMAT LIST SPAREPART INTERNAL CELL ── */
        .part-list {
            line-height: 1.4;
            font-weight: bold;
            text-transform: uppercase;
        }

        .part-qty {
            font-weight: 600;
            color: #cf0707;
        }

        /* Gaya Khusus Warna Biru Untuk Teks Counter CL */
        .text-cl-blue {
            color: #0026e6;
            font-weight: bold;
        }

        /* ── TANDA TANGAN ── */
        .signature-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .signature-box {
            text-align: center;
            width: 220px;
        }

        .signature-box .lbl {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 50px;
        }

        .signature-box .line {
            border-top: 1px solid #111;
            font-size: 10px;
            font-weight: 700;
            padding-top: 4px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .report-table thead th {
                background: #ffea31 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .row-rayon-divider {
                background-color: #cbd5e1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .text-cl-blue {
                color: #0026e6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>DAFTAR GANTI PART</h1>

        <div class="sub-header-info">
            <div class="periode-box">
                Periode Bulan: {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM Y') }}
            </div>
            <div class="rayon-box">
                PT. DINAMIKA GLOBAL GEMILANG
            </div>
        </div>
    </div>

    <table class="report-table">
        <colgroup>
            <col style="width: 65px;">
            <col style="width: 160px;">
            <col style="width: 70px;">
            <col style="width: 90px;">
            <col>
            <col style="width: 105px;">
            <col style="width: 105px;">
            <col style="width: 90px;">
        </colgroup>
        <thead>
            <tr class="h1">
                <th rowspan="2">TANGGAL</th>
                <th rowspan="2">NAMA CUSTOMER</th>
                <th rowspan="2">TYPE</th>
                <th rowspan="2">NO SERI</th>
                <th rowspan="2">NAMA PART</th>
                <th colspan="2">RIWAYAT METERAN COUNTER MESIN</th>
                <th rowspan="2">TEKNISI</th>
            </tr>
            <tr class="h2">
                <th>1 BULAN LALU (BW/CL)</th>
                <th>AKHIR SEKARANG (BW/CL)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedUsages as $rayonName => $items)

                <tr class="row-rayon-divider">
                    <td colspan="8" class="tl" style="padding: 6px 10px;">
                        📍 RAYON: {{ strtoupper($rayonName) }}
                    </td>
                </tr>

                @php
                    // Gabungkan baris yang memiliki serial_number DAN tanggal yang sama
                    $groupedBySerialAndDate = collect($items)
                        ->groupBy(function ($item) {
                            return $item->serial_number .
                                '_' .
                                ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Ymd') : 'nodate');
                        })
                        ->sortKeys();
                @endphp

                @foreach ($groupedBySerialAndDate as $combinedKey => $partsGroup)
                    @php
                        $first = $partsGroup->first();

                        // Kumpulkan nama part menyamping dipisah koma
                        $combinedParts = $partsGroup
                            ->groupBy('nama_part')
                            ->map(function ($g) {
                                $firstPart = $g->first();
                                $qty = $g->sum('jumlah_part') ?: $g->sum('qty') ?: $g->sum('jumlah') ?: 1;
                                return strtoupper($firstPart->nama_part ?? '-') . ($qty > 1 ? " ({$qty})" : '');
                            })
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td class="tc">
                            {{ $first->tanggal ? \Carbon\Carbon::parse($first->tanggal)->format('d/m/Y') : '-' }}
                        </td>

                        <td class="tl font-bold">{{ $first->nama_customer ?? '-' }}</td>

                        <td class="tc">{{ $first->tipe_model ?? '-' }}</td>

                        <td class="tc mono font-bold">{{ $first->serial_number ?? '-' }}</td>

                        <td class="tl">
                            <div class="part-list">{{ $combinedParts }}</div>
                        </td>

                        <td class="tc mono">
                            @if (($first->usage_color ?? 0) > 0 || ($first->usage_bw ?? 0) > 0)
                                @if (($first->usage_color ?? 0) > 0)
                                    BW: {{ number_format($first->usage_bw ?? 0) }}<br><span class="text-cl-blue">CL:
                                        {{ number_format($first->usage_color) }}</span>
                                @else
                                    {{ number_format($first->usage_bw ?? 0) }}
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        <td class="tc mono" style="background-color: #fffde7;">
                            @if (($first->counter_color ?? 0) > 0 || ($first->counter_bw ?? 0) > 0)
                                @if (($first->counter_color ?? 0) > 0)
                                    BW: {{ number_format($first->counter_bw ?? 0) }}<br><span class="text-cl-blue">CL:
                                        {{ number_format($first->counter_color) }}</span>
                                @else
                                    {{ number_format($first->counter_bw ?? 0) }}
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        <td class="tc" style="text-transform: uppercase;">
                            {{ $first->nama_technician ?? ($first->teknisi ?? '-') }}
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="tc" style="padding: 30px; color: #999; font-weight: bold;">
                        Tidak ada data pemakaian sparepart mesin pada periode bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">
                Cirebon, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
            </div>
            <div class="line">ADMIN GUDANG</div>
        </div>
    </div>

    <script>
        window.onload = () => setTimeout(() => window.print(), 500);
    </script>

</body>

</html>
