<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Ganti Part & Pengeluaran Part — Periode {{ $month }}/{{ $year }}</title>
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

        /* ── BARIS TOTAL ── */
        .row-grand-total td {
            background-color: #fffde7 !important;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ── TABEL REKAP ── */
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

        .font-bold {
            font-weight: 700;
        }

        .mono {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            font-weight: 600;
        }

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

        .customer-list {
            line-height: 1.5;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* ── TABEL MATRIX PART x TANGGAL ── */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .matrix-table th,
        .matrix-table td {
            border: 1px solid #000;
            text-align: center;
            padding: 3px 2px;
            font-size: 8px;
        }

        .matrix-table thead th {
            background: #ffea31;
            font-weight: 700;
            text-transform: uppercase;
        }

        .matrix-table th.part-name-col,
        .matrix-table td.part-name-col {
            text-align: left;
            padding-left: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .matrix-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .matrix-table td.has-qty {
            font-weight: 700;
            color: #dc2626;
            background-color: #fff5f5;
        }

        .matrix-table td.total-col {
            font-weight: 700;
            background-color: #fffde7;
        }

        .matrix-table tfoot td {
            font-weight: 700;
            background-color: #fffde7;
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

        /* ── PEMISAH HALAMAN CETAK ── */
        .page-break {
            page-break-before: always;
            break-before: page;
            margin-top: 0;
        }

        thead {
            display: table-header-group;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .report-table thead th,
            .recap-table thead th,
            .matrix-table thead th {
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

            .part-qty-red,
            .matrix-table td.has-qty {
                color: #dc2626 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .matrix-table td.has-qty {
                background-color: #fff5f5 !important;
            }

            .matrix-table td.total-col,
            .matrix-table tfoot td,
            .row-grand-total td {
                background-color: #fffde7 !important;
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

    {{-- ======================================================= --}}
    {{-- NORMALISASI DATA (SATU SUMBER KEBENARAN UNTUK SEMUA TABEL) --}}
    {{-- ======================================================= --}}
    @php
        /**
         * Pisahkan nama part bersih & qty.
         * - "TONER X BLACK (3 PCS)" => ["TONER X BLACK", 3]
         * - Jika nama tidak memuat "(N PCS)", pakai kolom jumlah/qty/jumlah_part, default 1.
         */
        $parsePart = function ($item) {
            $raw = strtoupper(trim((string) ($item->nama_part ?? '-')));

            $qtyFromName = null;
            if (preg_match('/\(\s*(\d+)\s*PCS\s*\)/i', $raw, $m)) {
                $qtyFromName = (int) $m[1];
            }

            $clean = preg_replace('/\(\s*\d+\s*PCS\s*\)/i', '', $raw);
            $clean = trim(preg_replace('/\s+/', ' ', $clean));
            if ($clean === '') {
                $clean = '-';
            }

            $explicit = $item->jumlah ?? ($item->qty ?? ($item->jumlah_part ?? null));
            $qty = $qtyFromName ?? ($explicit !== null ? (int) $explicit : 1);

            return [$clean, max(1, $qty)];
        };

        // Label part + qty merah bila > 1 (nama sudah di-escape)
        $partLabel = function ($name, $qty) {
            return e($name) . ($qty > 1 ? ' <span class="part-qty-red">(' . $qty . ')</span>' : '');
        };

        // Ratakan semua data jadi satu koleksi baris yang sudah bersih
        $rows = collect();
        foreach ($groupedUsages as $rayonName => $items) {
            foreach ($items as $item) {
                [$cleanName, $qty] = $parsePart($item);

                $custName = trim((string) ($item->nama_customer ?? ''));

                $rows->push(
                    (object) [
                        'rayon' => strtoupper((string) $rayonName),
                        'date_key' => !empty($item->tanggal)
                            ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d')
                            : null,
                        'customer' => $custName === '' ? '-' : $custName,
                        'tipe' => $item->tipe_model ?? '-',
                        'serial' => $item->serial_number ?? '-',
                        'part' => $cleanName,
                        'qty' => $qty,
                        'usage_bw' => $item->usage_bw ?? 0,
                        'usage_color' => $item->usage_color ?? 0,
                        'counter_bw' => $item->counter_bw ?? 0,
                        'counter_color' => $item->counter_color ?? 0,
                        'teknisi' => $item->nama_technician ?? ($item->teknisi ?? '-'),
                    ],
                );
            }
        }

        $rows = $rows->sortBy(fn($r) => $r->date_key ?? '9999-12-31')->values();

        $periodeLabel = \Carbon\Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM Y');
        $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
        $tanggalCetak = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y');
    @endphp

    {{-- ======================================================= --}}
    {{-- HALAMAN 1 — DAFTAR GANTI PART                            --}}
    {{-- ======================================================= --}}

    <div class="kop">
        <h1>DAFTAR GANTI PART</h1>

        <div class="sub-header-info">
            <div class="periode-box">
                Periode Bulan: {{ $periodeLabel }}
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
            @forelse ($rows->groupBy('rayon') as $rayonName => $rayonRows)
                <tr class="row-rayon-divider">
                    <td colspan="8" class="tl" style="padding: 6px 10px;">
                        📍 RAYON: {{ $rayonName }}
                    </td>
                </tr>

                @foreach ($rayonRows->groupBy(fn($r) => $r->serial . '_' . ($r->date_key ?? 'nodate')) as $partsGroup)
                    @php
                        $first = $partsGroup->first();

                        // Gabung part yang namanya sama (qty dijumlahkan)
                        $combinedParts = $partsGroup
                            ->groupBy('part')
                            ->map(fn($g, $name) => $partLabel($name, $g->sum('qty')))
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td class="tc">
                            {{ $first->date_key ? \Carbon\Carbon::parse($first->date_key)->format('d/m/Y') : '-' }}
                        </td>

                        <td class="tl font-bold">{{ $first->customer }}</td>

                        <td class="tc">{{ $first->tipe }}</td>

                        <td class="tc mono font-bold">{{ $first->serial }}</td>

                        <td class="tl">
                            <div class="part-list">{!! $combinedParts !!}</div>
                        </td>

                        <td class="tc mono">
                            @if ($first->usage_color > 0 || $first->usage_bw > 0)
                                @if ($first->usage_color > 0)
                                    BW: {{ number_format($first->usage_bw) }}<br><span class="text-cl-blue">CL:
                                        {{ number_format($first->usage_color) }}</span>
                                @else
                                    {{ number_format($first->usage_bw) }}
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        <td class="tc mono" style="background-color: #fffde7;">
                            @if ($first->counter_color > 0 || $first->counter_bw > 0)
                                @if ($first->counter_color > 0)
                                    BW: {{ number_format($first->counter_bw) }}<br><span class="text-cl-blue">CL:
                                        {{ number_format($first->counter_color) }}</span>
                                @else
                                    {{ number_format($first->counter_bw) }}
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        <td class="tc" style="text-transform: uppercase;">
                            {{ $first->teknisi }}
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

    @if ($rows->isNotEmpty())
        @php
            // [NAMA PART => total qty]
            $sortedGlobalParts = $rows->groupBy('part')->map(fn($g) => $g->sum('qty'))->sortKeys();

            // [NAMA PART => [NAMA CUSTOMER => total qty]]
            $partCustomers = $rows
                ->groupBy('part')
                ->map(fn($g) => $g->groupBy('customer')->map(fn($c) => $c->sum('qty'))->sortKeys())
                ->sortKeys();

            // [NAMA PART => [tanggal(1..31) => total qty]]  (hanya baris yang punya tanggal)
            $partDateMatrix = $rows
                ->whereNotNull('date_key')
                ->groupBy('part')
                ->map(fn($g) => $g->groupBy(fn($r) => (int) substr($r->date_key, 8, 2))->map(fn($d) => $d->sum('qty')))
                ->sortKeys();

            // Total per tanggal (baris paling bawah matrix)
            $dayTotals = [];
            foreach ($partDateMatrix as $map) {
                foreach ($map as $day => $q) {
                    $dayTotals[$day] = ($dayTotals[$day] ?? 0) + $q;
                }
            }
        @endphp

        <!-- ═══ REKAPITULASI TOTAL PEMAKAIAN SPAREPART ═══ -->
        <div class="recap-section-title page-break">Rekapitulasi Total Pemakaian Sparepart</div>
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
                @foreach ($sortedGlobalParts as $partName => $totalQty)
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td class="tl font-bold">{{ $partName }}</td>
                        <td class="tc part-qty-red" style="font-size: 11px;">{{ $totalQty }}</td>
                    </tr>
                @endforeach
                <tr class="row-grand-total">
                    <td colspan="2" class="tc">TOTAL SELURUH SPAREPART</td>
                    <td class="tc" style="font-size: 11px;">{{ $sortedGlobalParts->sum() }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ═══ DAFTAR CUSTOMER PER SPAREPART ═══ -->
        <div class="recap-section-title page-break">
            Daftar Customer Per Sparepart — {{ $periodeLabel }}
        </div>
        <table class="recap-table">
            <colgroup>
                <col style="width: 50px;">
                <col style="width: 220px;">
                <col style="width: 90px;">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA SPAREPART</th>
                    <th>TOTAL KELUAR</th>
                    <th>KELUAR DI CUSTOMER</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($partCustomers as $partNameCust => $customerList)
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td class="tl font-bold part-list">{{ $partNameCust }}</td>
                        <td class="tc part-qty-red" style="font-size: 11px;">{{ $customerList->sum() }}</td>
                        <td class="tl">
                            <div class="customer-list">
                                @foreach ($customerList as $custNameLoop => $custQtyLoop)
                                    {{ $custNameLoop }}@if ($custQtyLoop > 1)
                                        <span class="part-qty-red">({{ $custQtyLoop }})</span>
                                    @endif
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ═══ MATRIX: NAMA PART (BARIS) x TANGGAL (KOLOM) ═══ -->
        <div class="recap-section-title page-break">
            Matrix Pemakaian Sparepart Per Tanggal — {{ $periodeLabel }}
        </div>
        <table class="matrix-table">
            <colgroup>
                <col style="width: 140px;">
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <col>
                @endfor
                <col style="width: 45px;">
            </colgroup>
            <thead>
                <tr>
                    <th class="part-name-col">NAMA SPAREPART</th>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        <th>{{ $d }}</th>
                    @endfor
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($partDateMatrix as $partNameMx => $dateQtyMap)
                    <tr>
                        <td class="part-name-col">{{ $partNameMx }}</td>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php $qtyOnDay = $dateQtyMap[$d] ?? 0; @endphp
                            <td class="{{ $qtyOnDay > 0 ? 'has-qty' : '' }}">
                                {{ $qtyOnDay > 0 ? $qtyOnDay : '' }}
                            </td>
                        @endfor
                        <td class="total-col">{{ $dateQtyMap->sum() }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="part-name-col">TOTAL PER TANGGAL</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        <td>{{ ($dayTotals[$d] ?? 0) > 0 ? $dayTotals[$d] : '' }}</td>
                    @endfor
                    <td>{{ array_sum($dayTotals) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">
                Cirebon, {{ $tanggalCetak }}
            </div>
            <div class="line">ADMIN GUDANG</div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- HALAMAN 2 — DAFTAR PENGELUARAN PART (Tgl 1 s/d akhir)    --}}
    {{-- ======================================================= --}}

    <div class="kop page-break">
        <h1>DAFTAR PENGELUARAN PART</h1>

        <div class="sub-header-info">
            <div class="periode-box">
                Periode Tanggal 1 s/d {{ $daysInMonth }} — {{ $periodeLabel }}
            </div>
            <div class="rayon-box">
                PT. DINAMIKA GLOBAL GEMILANG
            </div>
        </div>
    </div>

    <!-- TABEL PENGELUARAN PART HARIAN -->
    <table class="report-table">
        <colgroup>
            <col style="width: 65px;">
            <col style="width: 160px;">
            <col style="width: 90px;">
            <col>
            <col style="width: 70px;">
            <col style="width: 130px;">
        </colgroup>
        <thead>
            <tr class="h1">
                <th>TANGGAL</th>
                <th>NAMA CUSTOMER</th>
                <th>NO SERI</th>
                <th>NAMA PART KELUAR</th>
                <th>JUMLAH</th>
                <th>TEKNISI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows->groupBy(fn($r) => $r->date_key ?? 'Tanpa Tanggal') as $tanggalKey => $itemsOnDate)
                @php
                    $tanggalLabel =
                        $tanggalKey !== 'Tanpa Tanggal' ? \Carbon\Carbon::parse($tanggalKey)->format('d/m/Y') : '-';
                @endphp

                @foreach ($itemsOnDate->groupBy(fn($r) => $r->serial . '_' . $r->customer) as $partsGroup)
                    @php
                        $first = $partsGroup->first();

                        // Gabung part sama (qty dijumlahkan), lalu hitung total baris dari qty yang sudah benar
                        $mergedParts = $partsGroup->groupBy('part')->map(fn($g) => $g->sum('qty'));
                        $totalQtyRow = $mergedParts->sum();
                        $combinedPartsLabel = $mergedParts
                            ->map(fn($qty, $name) => $partLabel($name, $qty))
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td class="tc">{{ $tanggalLabel }}</td>
                        <td class="tl font-bold">{{ $first->customer }}</td>
                        <td class="tc mono font-bold">{{ $first->serial }}</td>
                        <td class="tl">
                            <div class="part-list">{!! $combinedPartsLabel !!}</div>
                        </td>
                        <td class="tc part-qty-red" style="font-size: 11px;">{{ $totalQtyRow }}</td>
                        <td class="tc" style="text-transform: uppercase;">{{ $first->teknisi }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="tc" style="padding: 30px; color: #999; font-weight: bold;">
                        Tidak ada data pengeluaran sparepart pada periode tanggal 1 s/d akhir bulan ini.
                    </td>
                </tr>
            @endforelse

            @if ($rows->isNotEmpty())
                <tr class="row-grand-total">
                    <td colspan="4" class="tc">TOTAL PART KELUAR</td>
                    <td class="tc" style="font-size: 11px;">{{ $rows->sum('qty') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">
                Cirebon, {{ $tanggalCetak }}
            </div>
            <div class="line">ADMIN GUDANG</div>
        </div>
    </div>

    <script>
        window.onload = () => setTimeout(() => window.print(), 500);
    </script>

</body>

</html>
