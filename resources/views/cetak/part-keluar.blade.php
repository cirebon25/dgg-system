<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kebutuhan Part {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 30px;
        }

        .kop {
            text-align: center;
            margin-bottom: 10px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .kop h4 {
            margin: 4px 0 0;
            font-weight: normal;
            font-size: 14px;
        }

        .filter {
            text-align: center;
            font-size: 12px;
            color: #333;
            margin-top: 6px;
        }

        .garis {
            border: 0;
            border-top: 2px solid #000;
            margin: 12px 0 18px;
        }

        .cust-title {
            margin: 22px 0 6px;
            font-size: 14px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        table.data th {
            background: #e9e9e9;
            text-align: center;
        }

        tr.part-head td {
            background: #f0f0f0;
            font-weight: bold;
        }

        tr.part-sub td {
            background: #fafafa;
            font-weight: bold;
        }

        table.data tfoot th {
            background: #e9e9e9;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .grand {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .ttd {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            height: 100px;
            border: none;
        }

        .tgl-cetak {
            margin-top: 25px;
            font-size: 11px;
            color: #555;
        }

        .toolbar {
            margin-bottom: 15px;
        }

        .toolbar button {
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            border: 1px solid #333;
            background: #fff;
            border-radius: 4px;
        }

        .toolbar button:hover {
            background: #eee;
        }

        @media print {
            body {
                margin: 15mm;
            }

            .no-print {
                display: none !important;
            }

            table.data tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="toolbar no-print">
        <button onclick="window.print()">🖨 Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="kop">
        <h2>LAPORAN KEBUTUHAN PART</h2>
        <h4>Periode: {{ $bulan }} {{ $tahun }}</h4>
        @if ($filterCustomer || $filterParts)
            <div class="filter">
                @if ($filterCustomer)
                    Customer: <strong>{{ $filterCustomer }}</strong>
                @endif
                @if ($filterCustomer && $filterParts)
                    &nbsp;|&nbsp;
                @endif
                @if ($filterParts)
                    Part: <strong>{{ $filterParts }}</strong>
                @endif
            </div>
        @endif
    </div>

    <hr class="garis">

    @forelse ($perCustomer as $c)
        <div class="cust-title">{{ $loop->iteration }}. {{ $c->nama }}</div>

        <table class="data">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>No Seri</th>
                    <th style="width:100px">Dipakai (x)</th>
                    <th style="width:120px">Jumlah (unit)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($c->parts as $p)
                    <tr class="part-head">
                        <td colspan="4">
                            {{ $p->sparepart->nama_sparepart ?? '-' }}
                            @if (!empty($p->sparepart->no_part))
                                &nbsp;(No Part: {{ $p->sparepart->no_part }})
                            @endif
                        </td>
                    </tr>

                    @foreach ($p->machines as $i => $m)
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $m->sn }}</td>
                            <td class="center">{{ $m->kali }}</td>
                            <td class="center">{{ number_format($m->total) }}</td>
                        </tr>
                    @endforeach

                    <tr class="part-sub">
                        <td colspan="3" class="right">Subtotal {{ $p->sparepart->nama_sparepart ?? '-' }}</td>
                        <td class="center">{{ number_format($p->total) }}</td>
                    </tr>
                @endforeach
            </tbody>

            @if ($c->parts->count() > 1)
                <tfoot>
                    <tr>
                        <th colspan="3" class="right">Total {{ $c->nama }}</th>
                        <th class="center">{{ number_format($c->total) }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    @empty
        <table class="data">
            <tr>
                <td class="center">Tidak ada part keluar pada periode ini.</td>
            </tr>
        </table>
    @endforelse

    @if ($perCustomer->count() > 1)
        <div class="grand">Total Keseluruhan: {{ number_format($grand) }} unit</div>
    @endif

    <table class="ttd">
        <tr>
            <td>Dibuat oleh,<br><br><br><br>( ____________________ )</td>
            <td>Mengetahui,<br><br><br><br>( ____________________ )</td>
        </tr>
    </table>

    <div class="tgl-cetak">
        Dicetak: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>
