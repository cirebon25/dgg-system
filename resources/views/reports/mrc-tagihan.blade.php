<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tagihan MRC - {{ $bulan ?? '' }} {{ $tahun ?? '' }}</title>
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
            margin-bottom: 6px;
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
            text-align: left;
        }

        td.center,
        th.center {
            text-align: center;
        }

        td.right,
        th.right {
            text-align: right;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
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
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">🖨️
            Print</button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">✕
            Tutup</button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Tagihan MRC — {{ $bulan ?? '' }} {{ $tahun ?? '' }}</h2>
            <p>Periode &nbsp;&nbsp;: {{ $bulan ?? '' }} {{ $tahun ?? '' }}</p>
            <p>Dicetak &nbsp;&nbsp;: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
        <hr>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Customer</th>
                    <th style="width:90px;">SN Mesin</th>
                    <th style="width:75px;">Model</th>
                    <th class="right" style="width:70px;">Harga Sewa</th>
                    <th class="center" style="width:50px;">Usage BW</th>
                    <th class="center" style="width:45px;">Free BW</th>
                    <th class="center" style="width:50px;">Lebih BW</th>
                    <th class="right" style="width:65px;">Biaya BW</th>
                    <th class="center" style="width:50px;">Usage CL</th>
                    <th class="center" style="width:45px;">Free CL</th>
                    <th class="center" style="width:50px;">Lebih CL</th>
                    <th class="right" style="width:65px;">Biaya CL</th>
                    <th class="right" style="width:75px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse ($tagihans ?? [] as $i => $item)
                    @php
                        $t = $item['tagihan'];
                        $grandTotal += $t['total'];
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $item['contract']->customer?->nama_customer ?? '-' }}</td>
                        <td>{{ $item['contract']->machine?->serial_number ?? '-' }}</td>
                        <td>{{ $item['contract']->machine?->tipe_model ?? '-' }}</td>
                        <td class="right">{{ number_format($t['harga_sewa']) }}</td>
                        <td class="center">{{ number_format($t['usage_bw']) }}</td>
                        <td class="center">{{ number_format($t['free_bw']) }}</td>
                        <td class="center">{{ $t['kelebihan_bw'] > 0 ? number_format($t['kelebihan_bw']) : '-' }}</td>
                        <td class="right">{{ $t['biaya_bw'] > 0 ? number_format($t['biaya_bw']) : '-' }}</td>
                        <td class="center">{{ number_format($t['usage_color']) }}</td>
                        <td class="center">{{ number_format($t['free_color']) }}</td>
                        <td class="center">{{ $t['kelebihan_color'] > 0 ? number_format($t['kelebihan_color']) : '-' }}
                        </td>
                        <td class="right">{{ $t['biaya_color'] > 0 ? number_format($t['biaya_color']) : '-' }}</td>
                        <td class="right" style="font-weight:bold;">Rp {{ number_format($t['total']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="center" style="padding:16px;">Tidak ada data tagihan.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13" class="right">Grand Total</td>
                    <td class="right">Rp {{ number_format($grandTotal) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Kepala Cabang</p>
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
