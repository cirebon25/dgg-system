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

        tbody tr.baris:nth-child(odd) {
            background: #f8fafc;
        }

        tbody tr.baris:nth-child(even) {
            background: #ffffff;
        }

        tr.group-title td {
            background: #e5e7eb;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
            color: #1e3a5f;
            padding: 8px 10px;
            border-color: #9ca3af;
        }

        tr.subtotal td {
            background: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #1e3a5f;
        }

        .col-no {
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
        }

        .empty {
            color: #9ca3af;
            font-style: italic;
        }

        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        @media print {
            body {
                padding: 12px;
            }

            thead tr,
            tr.group-title td,
            tr.subtotal td,
            .col-total,
            .col-avg {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr.group-title {
                page-break-after: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div class="company">DINAMIKA GLOBAL GEMILANG (DGG)</div>
        <div class="title">Ranking Pemakaian Customer per Rayon</div>
        <div class="periode">
            Periode: {{ strtoupper($bulan) }} {{ $tahun }} &nbsp;|&nbsp;
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="text-left">NAMA CUSTOMER</th>
                <th>SN MESIN</th>
                <th>PEMAKAIAN BW</th>
                <th>PEMAKAIAN COLOR</th>
                <th>TOTAL PEMAKAIAN</th>
                <th>RATA-RATA / BULAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr class="group-title">
                    <td colspan="7">
                        RAYON: {{ strtoupper($group->nama) }} ({{ $group->baris->count() }} customer)
                    </td>
                </tr>

                @foreach ($group->baris as $row)
                    <tr class="baris">
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td class="text-left"><strong>{{ $row->nama_customer }}</strong></td>
                        <td>{{ $row->serial_number }}</td>
                        <td class="col-bw">
                            {{ $row->pemakaian_bw !== null ? number_format($row->pemakaian_bw) : '-' }}
                        </td>
                        <td class="col-color">
                            {{ $row->pemakaian_color !== null ? number_format($row->pemakaian_color) : '-' }}
                        </td>
                        <td class="col-total">
                            {{ $row->pemakaian_total !== null ? number_format($row->pemakaian_total) : '-' }}
                        </td>
                        <td class="col-avg">{{ number_format($row->rata_rata) }}</td>
                    </tr>
                @endforeach

                <tr class="subtotal">
                    <td colspan="3" class="text-left">SUBTOTAL RAYON {{ strtoupper($group->nama) }}</td>
                    <td>{{ number_format($group->sub_bw) }}</td>
                    <td>{{ number_format($group->sub_color) }}</td>
                    <td>{{ number_format($group->sub_total) }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">Tidak ada data pemakaian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>DGG System &copy; {{ date('Y') }} &mdash; Developer RUDIANTO</span>
        <span>Total Customer: {{ $totalCustomer }}</span>
    </div>

</body>

</html>
