<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Part BDG - {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 8mm 12mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }

        .header p {
            text-align: left;
            margin-top: 3px;
            font-size: 11px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            border: 1px solid #333;
            padding: 5px 6px;
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }

        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            font-size: 11px;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background-color: #e0e0e0;
        }

        .footer {
            margin-top: 24px;
            text-align: left;
        }

        .ttd {
            display: inline-block;
            width: 180px;
            text-align: center;
        }

        .ttd .line {
            margin-top: 55px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 210mm;
            margin: 10px auto;
            padding: 0 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: #fff;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 12mm 8mm 12mm 8mm;
                margin: 0;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom:10px; padding-top:10px;">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">

        <div class="header">
            <h2>Laporan Saldo Sparepart</h2>
            <p>Periode &nbsp;&nbsp;: {{ $bulan }} {{ $tahun }}</p>
            <p>Dicetak &nbsp;&nbsp;: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
        </div>

        <hr>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:34px;">No</th>
                    <th style="width:115px;">No Part</th>
                    <th>Nama Part</th>
                    <th style="width:95px;">Kode Part</th>
                    <th class="center" style="width:70px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($spareparts as $i => $item)
                    @php
                        $total = ($item->stok ?? 0) + ($item->saldo_teknisi ?? 0);
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $item->no_part ?? '-' }}</td>
                        <td>{{ $item->nama_sparepart }}</td>
                        <td>{{ $item->code_part ?? '-' }}</td>
                        <td class="center">{{ $total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center" style="padding:16px; color:#888;">
                            Tidak ada data sparepart.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="font-weight:bold;">Total Item</td>
                    <td class="center" style="font-weight:bold;">
                        {{ $spareparts->sum(fn($s) => ($s->stok ?? 0) + ($s->saldo_teknisi ?? 0)) }}
                    </td>
                </tr>
            </tfoot>
            <tfoot>
                <tr>
                    <td colspan="4">Total Item</td>
                    <td class="center">{{ $spareparts->count() }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Cirebon, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p style="margin-top:4px;">Mengetahui,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>

    </div>

</body>

</html>
