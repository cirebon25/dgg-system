<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saldo Sparepart — PT DGG</title>
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

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #111827;
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── TOOLBAR ── */
        .toolbar {
            background: #f3f4f6;
            color: #374151;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .toolbar-hint {
            color: #6b7280;
        }

        .btn {
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
        }

        .btn:hover {
            background: #f9fafb;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
            margin-right: 6px;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }

        /* ── PAGE ── */
        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 20px 40px;
        }

        /* ── KOP SURAT ── */
        .kop {
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .kop-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .kop-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #111827;
        }

        .kop-sub {
            font-size: 12px;
            color: #4b5563;
            margin-top: 2px;
        }

        .kop-meta {
            text-align: right;
            font-size: 12px;
            color: #4b5563;
            line-height: 1.5;
        }

        .kop-meta strong {
            color: #111827;
            font-weight: 600;
        }

        .kop-banner {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── STATS CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .sc-kosong .stat-value {
            color: #dc2626;
        }

        .stat-unit {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── FORMULA ── */
        .formula {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .fx-label {
            font-weight: 600;
            color: #374151;
            margin-right: 4px;
        }

        .f-bold {
            color: #111827;
            font-weight: 600;
        }

        .f-op {
            color: #9ca3af;
            margin: 0 4px;
        }

        /* ── TABLE ── */
        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        /* Headers */
        thead tr.h1 {
            background: #f3f4f6;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
        }

        thead tr.h1 th {
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border-right: 1px solid #e5e7eb;
            letter-spacing: .5px;
        }

        thead tr.h1 th:last-child {
            border-right: none;
        }

        .th-saldo-span {
            text-align: center !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        thead tr.h2 {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        thead tr.h2 th {
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            border-right: 1px solid #e5e7eb;
            color: #4b5563;
        }

        thead tr.h2 th:last-child {
            border-right: none;
        }

        /* Body Rows */
        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr.row-empty {
            background: #fef2f2 !important;
        }

        tbody tr.row-empty td {
            color: #991b1b;
        }

        td {
            padding: 9px 12px;
            font-size: 12.5px;
            vertical-align: middle;
            border-right: 1px solid #e5e7eb;
        }

        td:last-child {
            border-right: none;
        }

        .td-no {
            text-align: center;
            color: #6b7280;
            width: 5%;
        }

        .td-nopart {
            text-align: center;
            font-weight: 600;
            width: 15%;
        }

        .td-kode {
            color: #4b5563;
            width: 15%;
        }

        .td-nama {
            font-weight: 500;
        }

        .td-stok {
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
            width: 11%;
        }

        .td-stok small {
            font-weight: 400;
            color: #6b7280;
            font-size: 11px;
        }

        .badge-kosong {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            margin-left: 6px;
            vertical-align: middle;
        }

        .col-total {
            background-color: #fafafa;
            font-weight: 600;
        }

        /* ── FOOTER / GRAND TOTAL ── */
        tfoot {
            display: none;
        }

        @media print {
            tfoot {
                display: table-footer-group !important;
            }
        }

        tfoot tr {
            background: #f3f4f6;
            border-top: 2px solid #d1d5db;
        }

        tfoot td {
            padding: 10px 12px;
            font-weight: 700;
            font-size: 12.5px;
        }

        .tfoot-label {
            text-align: right;
            color: #374151;
        }

        .grand-total-screen {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 6px 6px;
            display: flex;
            align-items: center;
        }

        .gt-label {
            flex: 1;
            padding: 12px 14px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            color: #374151;
            text-align: right;
            border-right: 1px solid #e5e7eb;
        }

        .gt-cell {
            padding: 12px 16px;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            width: 13%;
            border-right: 1px solid #e5e7eb;
            color: #111827;
        }

        .gt-cell:last-child {
            border-right: none;
            background: #f3f4f6;
        }

        .gt-cell small {
            font-size: 11px;
            font-weight: 400;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }

        /* ── PRINT ── */
        @media print {
            .toolbar {
                display: none !important;
            }

            .grand-total-screen {
                display: none !important;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .page {
                padding: 0;
                max-width: 100%;
            }

            .kop {
                border-bottom: 2px solid #000;
            }

            .table-wrap {
                border: 1px solid #000;
            }

            thead tr.h1 {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead tr.h2 {
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr.row-empty {
                background: #fef2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tfoot tr {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="toolbar">
        <div class="toolbar-hint">PT DGG — Laporan Saldo Gudang Sparepart</div>
        <div>
            <button onclick="window.print()" class="btn btn-print">Cetak</button>
            <button onclick="window.close()" class="btn">Tutup</button>
        </div>
    </div>

    <div class="page">

        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="kop-head">
                <div>
                    <div class="kop-title">PT. Dinamika Global Gemilang</div>
                    <div class="kop-sub">Depo Cirebon &nbsp;&middot;&nbsp; Divisi Gudang &amp; Sparepart</div>
                </div>
                <div class="kop-meta">
                    <div>Tanggal Cetak: <strong>{{ $tanggalCetak }} WIB</strong></div>
                    <div>Total Item Terdaftar: <strong>{{ $summary['total_item'] }} Part</strong></div>
                </div>
            </div>
            <div class="kop-banner">Laporan Saldo Gudang Sparepart &mdash; Data Realtime</div>
        </div>

        {{-- STATS CARDS --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Saldo Aktif (Gudang)</div>
                <div class="stat-value">{{ number_format($summary['total_aktif']) }}</div>
                <div class="stat-unit">Pcs</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Saldo Tas Teknisi</div>
                <div class="stat-value">{{ number_format($summary['total_tas']) }}</div>
                <div class="stat-unit">Pcs</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Keseluruhan</div>
                <div class="stat-value">{{ number_format($summary['total_saldo']) }}</div>
                <div class="stat-unit">Pcs</div>
            </div>
            <div class="stat-card sc-kosong">
                <div class="stat-label">Stok Kosong</div>
                <div class="stat-value">{{ $summary['item_kosong'] }}</div>
                <div class="stat-unit">Part</div>
            </div>
        </div>

        {{-- FORMULA --}}
        <div class="formula">
            <span class="fx-label">Keterangan Rumus:</span>
            <span class="f-bold">Saldo Aktif (Gudang)</span>
            <span class="f-op">+</span>
            <span class="f-bold">Saldo Tas Teknisi</span>
            <span class="f-op">=</span>
            <span class="f-bold">Total Saldo Keseluruhan</span>
        </div>

        {{-- TABLE --}}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr class="h1">
                        <th rowspan="2" style="text-align:center; width:5%;">No</th>
                        <th rowspan="2" style="text-align:center; width:15%;">No Part</th>
                        <th rowspan="2" style="width:15%;">Kode Part</th>
                        <th rowspan="2">Nama Sparepart</th>
                        <th colspan="3" class="th-saldo-span" style="width:33%;">Saldo Sparepart</th>
                    </tr>
                    <tr class="h2">
                        <th style="width:11%;">Aktif (Gudang)</th>
                        <th style="width:11%;">Tas Teknisi</th>
                        <th style="width:11%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($spareparts as $index => $part)
                        @php $isEmpty = $part->total_saldo <= 0; @endphp
                        <tr class="{{ $isEmpty ? 'row-empty' : '' }}">
                            <td class="td-no">{{ $index + 1 }}</td>
                            <td class="td-nopart">{{ $part->no_part }}</td>
                            <td class="td-kode">{{ $part->code_part ?? '-' }}</td>
                            <td class="td-nama">
                                {{ strtoupper($part->nama_sparepart ?? '-') }}
                                @if ($isEmpty)
                                    <span class="badge-kosong">Kosong</span>
                                @endif
                            </td>
                            <td class="td-stok">{{ number_format($part->stok_aktif) }} <small>Pcs</small></td>
                            <td class="td-stok">{{ number_format($part->stok_tas) }} <small>Pcs</small></td>
                            <td class="td-stok col-total">{{ number_format($part->total_saldo) }} <small>Pcs</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px; color:#6b7280; font-size:13px;">
                                Belum ada data sparepart di gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="tfoot-label">Grand Total &mdash; {{ $summary['total_item'] }} Part :
                        </td>
                        <td class="td-stok">{{ number_format($summary['total_aktif']) }} <small>Pcs</small></td>
                        <td class="td-stok">{{ number_format($summary['total_tas']) }} <small>Pcs</small></td>
                        <td class="td-stok col-total">{{ number_format($summary['total_saldo']) }} <small>Pcs</small>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- GRAND TOTAL SCREEN --}}
        <div class="grand-total-screen">
            <div class="gt-label">Grand Total &mdash; {{ $summary['total_item'] }} Part :</div>
            <div class="gt-cell">{{ number_format($summary['total_aktif']) }}<small>Aktif (Gudang)</small></div>
            <div class="gt-cell">{{ number_format($summary['total_tas']) }}<small>Tas Teknisi</small></div>
            <div class="gt-cell">{{ number_format($summary['total_saldo']) }}<small>Total Saldo</small></div>
        </div>

    </div>
</body>

</html>
