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
        .report-table thead tr.h2 th,
        .recap-table thead th {
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

        .report-table td,
        .recap-table td {
            border: 1px solid #000;
            padding: 5px 5px;
            vertical-align: middle;
            font-size: 9.5px;
            word-wrap: break-word;
        }

        /* ── TABEL REKAP PER SPAREPART DI BAWAH ── */
        .recap-section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            margin-top: 25px;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .part-qty-red {
            font-weight: 700;
            color: #dc2626 !important;
        }

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

            .report-table thead th,
            .recap-table thead th {
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

            .part-qty-red {
                color: #dc2626 !important;
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

    <!-- TABEL UTAMA -->
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
                    $sortedItems = collect($items)->sortBy(function ($item) {
                        return $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '9999-12-31';
                    });

                    $groupedBySerialAndDate = $sortedItems->groupBy(function ($item) {
                        return $item->serial_number .
                            '_' .
                            ($item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Ymd') : 'nodate');
                    });
                @endphp

                @foreach ($groupedBySerialAndDate as $combinedKey => $partsGroup)
                    @php
                        $first = $partsGroup->first();
                        $combinedParts = $partsGroup
                            ->groupBy('nama_part')
                            ->map(function ($g) {
                                $firstPart = $g->first();

                                // Mengambil nilai kuantitas dari berbagai kemungkinan nama kolom data
                                $qty = $g->sum(function ($item) {
                                    return $item->jumlah ?? ($item->qty ?? ($item->jumlah_part ?? 1));
                                });

                                $partName = strtoupper($firstPart->nama_part ?? '-');

                                return $qty > 1
                                    ? "{$partName} <span class=\"part-qty-red\">({$qty})</span>"
                                    : "{$partName}";
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
                            <div class="part-list">{!! $combinedParts !!}</div>
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

    @if (count($groupedUsages) > 0)
        @php
            $globalAllParts = collect();
            foreach ($groupedUsages as $items) {
                foreach ($items as $item) {
                    $qty = $item->jumlah ?? ($item->qty ?? ($item->jumlah_part ?? 1));
                    $pName = strtoupper($item->nama_part ?? '-');
                    $globalAllParts->put($pName, $globalAllParts->get($pName, 0) + $qty);
                }
            }
            $sortedGlobalParts = $globalAllParts->sortKeys();
        @endphp

        <!-- TABEL REKAP BERDASARKAN NAMA SPAREPART -->
        <div class="recap-section-title">Rekapitulasi Total Pemakaian Sparepart</div>
        <table class="recap-table">
            <colgroup>
                <col style="width: 50px;">
                <col>
                <col style="width: 120px;">
            </colgroup>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA SPAREPART</th>
                    <th>TOTAL KELUAR</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($sortedGlobalParts as $partName => $totalQty)
                    <tr>
                        <td class="tc">{{ $no++ }}</td>
                        <td class="tl font-bold">{{ $partName }}</td>
                        <td class="tc part-qty-red" style="font-size: 11px;">{{ $totalQty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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
