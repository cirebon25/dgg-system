@php
    $fmt = fn($nilai) => $nilai === null ? '–' : number_format($nilai);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ranking Pemakaian DGG</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10.5px;
            color: #111827;
            background: #ffffff;
            padding: 16px 20px;
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .warna {
            color: #dc2626;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 14px;
            border-bottom: 2px solid #1e3a5f;
        }

        .header .company {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }

        .header .title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 3px;
        }

        .header .periode {
            font-size: 10px;
            color: #4b5563;
            margin-top: 3px;
        }

        /* ===== TABEL ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        thead th {
            background: #1e3a5f;
            color: #ffffff;
            padding: 7px 8px;
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-align: center;
            border: 1px solid #1e3a5f;
        }

        thead th.num {
            text-align: right;
        }

        thead th.left {
            text-align: left;
        }

        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            height: 26px;
        }

        tbody tr.baris {
            page-break-inside: avoid;
        }

        tbody tr.baris:nth-child(even) td {
            background: #fafafa;
        }

        .col-no {
            width: 34px;
            text-align: center;
            color: #6b7280;
        }

        .col-nama {
            font-weight: bold;
        }

        .col-sn {
            text-align: center;
            color: #374151;
            white-space: nowrap;
        }

        .counter {
            white-space: nowrap;
        }

        .counter .sep {
            color: #9ca3af;
            padding: 0 3px;
        }

        .col-total {
            font-weight: bold;
        }

        .col-rata {
            font-weight: bold;
            color: #1e3a5f;
        }

        .kosong {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }

        /* ===== JUDUL RAYON & SUBTOTAL ===== */
        tr.group-title td {
            background: #e8edf4;
            color: #1e3a5f;
            font-weight: bold;
            font-size: 11px;
            padding: 7px 8px;
            border-top: 1px solid #9ca3af;
            border-bottom: 1px solid #9ca3af;
            page-break-after: avoid;
        }

        tr.subtotal td {
            font-weight: bold;
            border-top: 2px solid #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding: 6px 8px;
        }

        tr.subtotal td.label {
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
        }

        @media print {
            body {
                padding: 0;
            }

            thead th,
            tr.group-title td,
            tr.subtotal td,
            tbody tr.baris:nth-child(even) td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div class="company">DINAMIKA GLOBAL GEMILANG (DGG)</div>
        <div class="title">RANKING PEMAKAIAN CUSTOMER PER RAYON</div>
        <div class="periode">
            Periode: {{ strtoupper($bulan) }} {{ $tahun }} &nbsp;&bull;&nbsp;
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="left">NAMA CUSTOMER</th>
                <th>SN MESIN</th>
                <th>COUNTER LALU<br><span style="font-weight: normal; font-size: 8.5px;">BW / COLOR</span></th>
                <th>COUNTER INI<br><span style="font-weight: normal; font-size: 8.5px;">BW / COLOR</span></th>
                <th class="num">PEMAKAIAN BW</th>
                <th class="num">PEMAKAIAN COLOR</th>
                <th class="num">TOTAL PEMAKAIAN</th>
                <th class="num">RATA-RATA / BULAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr class="group-title">
                    <td colspan="9">
                        RAYON {{ strtoupper($group->nama) }} &nbsp;&middot;&nbsp; {{ $group->baris->count() }} customer
                    </td>
                </tr>

                @foreach ($group->baris as $row)
                    <tr class="baris">
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td class="col-nama">{{ $row->nama_customer }}</td>
                        <td class="col-sn">{{ $row->serial_number }}</td>

                        @if ($row->counter_bw_ini === null)
                            <td colspan="5" class="kosong">
                                Belum ada service {{ strtoupper($bulan) }}
                            </td>
                        @else
                            <td class="counter num">
                                {{ $fmt($row->counter_bw_lalu) }}<span class="sep">/</span><span
                                    class="warna">{{ $fmt($row->counter_color_lalu) }}</span>
                            </td>
                            <td class="counter num">
                                {{ $fmt($row->counter_bw_ini) }}<span class="sep">/</span><span
                                    class="warna">{{ $fmt($row->counter_color_ini) }}</span>
                            </td>
                            <td class="num">{{ $fmt($row->pemakaian_bw) }}</td>
                            <td class="num warna">{{ $fmt($row->pemakaian_color) }}</td>
                            <td class="num col-total">{{ $fmt($row->pemakaian_total) }}</td>
                        @endif

                        <td class="num col-rata">{{ number_format($row->rata_rata) }}</td>
                    </tr>
                @endforeach

                <tr class="subtotal">
                    <td colspan="3" class="label">Subtotal rayon {{ strtolower($group->nama) }}</td>
                    <td colspan="2"></td>
                    <td class="num">{{ number_format($group->sub_bw) }}</td>
                    <td class="num warna">{{ number_format($group->sub_color) }}</td>
                    <td class="num">{{ number_format($group->sub_total) }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="kosong">Tidak ada data pemakaian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>DGG System &copy; {{ date('Y') }} &mdash; Developer RUDIANTO</span>
        <span>Total customer: {{ $totalCustomer }}</span>
    </div>

</body>

</html>
