<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saldo Sparepart — PT DGG</title>
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
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Toolbar (layar saja) */
        .toolbar {
            margin-bottom: 24px;
        }

        .btn {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
        }

        .btn-print {
            background: #2563eb;
        }

        .btn-close {
            background: #6b7280;
        }

        /* Judul */
        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .report-company {
            font-size: 13px;
        }

        .meta {
            margin-top: 2px;
            border-collapse: collapse;
        }

        .meta td {
            padding: 0 4px 0 0;
            border: none;
            font-size: 13px;
        }

        .divider {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0 12px;
        }

        /* Tabel */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 13px;
            vertical-align: middle;
        }

        table.data thead th {
            background: #e5e5e5;
            font-weight: bold;
            text-align: left;
        }

        .c {
            text-align: center;
        }

        .r {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .empty-data {
            text-align: center;
            padding: 20px 6px !important;
        }

        @media print {
            .toolbar {
                display: none !important;
            }

            .page {
                padding: 0;
                max-width: 100%;
            }

            table.data thead {
                display: table-header-group;
            }

            table.data tr {
                page-break-inside: avoid;
            }

            table.data thead th,
            .total-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="toolbar">
            <button type="button" onclick="window.print()" class="btn btn-print">Print</button>
            <button type="button" onclick="window.close()" class="btn btn-close">Tutup</button>
        </div>

        <div class="report-title">Laporan Saldo Sparepart</div>
        <div class="report-company">PT. Dinamika Global Gemilang &mdash; Depo Cirebon, Divisi Gudang &amp; Sparepart</div>
        <table class="meta">
            <tr>
                <td>Dicetak</td>
                <td>: {{ $tanggalCetak }} WIB</td>
            </tr>
            <tr>
                <td>Total Item</td>
                <td>: {{ number_format($summary['total_item']) }} Part ({{ number_format($summary['item_kosong']) }} Part kosong)</td>
            </tr>
        </table>

        <hr class="divider">

        <table class="data">
            <thead>
                <tr>
                    <th class="c" style="width:5%;">No</th>
                    <th style="width:14%;">No Part</th>
                    <th style="width:16%;">Kode Part</th>
                    <th>Nama Sparepart</th>
                    <th class="c" style="width:8%;">Gudang</th>
                    <th class="c" style="width:8%;">Tas Teknisi</th>
                    <th class="c" style="width:8%;">Total</th>
                    <th class="c" style="width:8%;">Ket.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($spareparts as $part)
                    <tr>
                        <td class="c">{{ $loop->iteration }}</td>
                        <td>{{ $part->no_part }}</td>
                        <td>{{ $part->code_part ?? '-' }}</td>
                        <td>{{ mb_strtoupper($part->nama_sparepart ?? '-') }}</td>
                        <td class="c">{{ number_format($part->stok_aktif) }}</td>
                        <td class="c">{{ number_format($part->stok_tas) }}</td>
                        <td class="c">{{ number_format($part->total_saldo) }}</td>
                        <td class="c">{{ $part->total_saldo <= 0 ? 'Kosong' : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-data">Belum ada data sparepart di gudang.</td>
                    </tr>
                @endforelse

                @if ($spareparts->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="4" class="r">Grand Total ({{ number_format($summary['total_item']) }} Part)</td>
                        <td class="c">{{ number_format($summary['total_aktif']) }}</td>
                        <td class="c">{{ number_format($summary['total_tas']) }}</td>
                        <td class="c">{{ number_format($summary['total_saldo']) }}</td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
</body>

</html>