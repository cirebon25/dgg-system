<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pemakaian Sparepart - {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #333;
            padding: 5px 6px;
            background: #d0d0d0;
            font-weight: bold;
            text-align: left;
        }

        td {
            border: 1px solid #333;
            padding: 4px 6px;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #888;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .ttd {
            text-align: center;
            width: 180px;
        }

        .ttd .line {
            margin-top: 55px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 210mm;
            margin: 10px auto;
            padding: 10px 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
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

    <div class="no-print">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Laporan Pemakaian Sparepart Bulanan</h2>
            <p>Periode &nbsp;&nbsp;: {{ $bulan }} {{ $tahun }}</p>
            <p>Dicetak &nbsp;&nbsp;: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
        <hr>

        @if ($data->isEmpty())
            <div class="no-data">
                Tidak ada pemakaian sparepart di periode ini, atau snapshot bulan lalu belum tersedia.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="center" style="width:30px;">No</th>
                        <th>Nama Sparepart</th>
                        <th style="width:90px;">Kode Part</th>
                        <th class="center" style="width:80px;">Stok Awal</th>
                        <th class="center" style="width:80px;">Stok Akhir</th>
                        <th class="center" style="width:90px;">Pemakaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $item)
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $item['sparepart']->nama_sparepart }}</td>
                            <td>{{ $item['sparepart']->code_part ?? '-' }}</td>
                            <td class="center">{{ $item['stok_awal'] }}</td>
                            <td class="center">{{ $item['stok_akhir'] }}</td>
                            <td class="center" style="font-weight:bold;">{{ $item['pemakaian'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">Total Pemakaian</td>
                        <td class="center">{{ $data->sum('pemakaian') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Kepala Gudang</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
            <div class="ttd">
                <p>Bandung, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p>Dibuat oleh,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>
    </div>

</body>

</html>
