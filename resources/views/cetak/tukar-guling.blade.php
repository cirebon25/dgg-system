<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Tukar Guling DGG</title>
    <style>
        @page {
            size: landscape;
            margin: 8mm;
        }

        body {
            font-family: sans-serif;
            font-size: 10px;
            padding: 10px;
            color: #333;
            margin: 0;
        }

        .header-box {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-box h1 {
            margin: 0;
            color: #000;
            font-size: 18px;
        }

        .header-box h2 {
            margin: 4px 0;
            color: #000;
            font-size: 13px;
        }

        .header-box p {
            font-weight: bold;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .th-normal {
            background-color: #f2f2f2;
            color: #000;
        }

        .th-awal {
            background-color: #2563eb;
            color: #fff;
        }

        .th-awal-sub {
            background-color: #93c5fd;
            color: #000;
        }

        .th-new {
            background-color: #16a34a;
            color: #fff;
        }

        .th-new-sub {
            background-color: #86efac;
            color: #000;
        }

        .td-awal {
            background-color: #eff6ff;
        }

        .td-new {
            background-color: #f0fdf4;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .empty-row td {
            text-align: center;
            padding: 20px;
            font-weight: bold;
            color: #666;
        }

        .ttd-box {
            margin-top: 35px;
            float: right;
            width: 220px;
            text-align: center;
        }

        .ttd-box p {
            margin: 5px 0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header-box">
        <h1>PT DINAMIKA GLOBAL GEMILANG</h1>
        <h2>LAPORAN TUKAR MESIN DGG CIREBON </h2>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="3%" class="th-normal">No</th>
                <th rowspan="2" width="7%" class="th-normal">Tgl</th>
                <th rowspan="2" width="14%" class="th-normal">Nama Customer</th>
                <th colspan="4" class="th-awal">UNIT AWAL (DITARIK)</th>
                <th colspan="4" class="th-new">UNIT BARU (TERPASANG)</th>
                <th rowspan="2" width="12%" class="th-normal">Keterangan Ganti</th>
            </tr>
            <tr>
                <th width="9%" class="th-awal-sub">Tipe Awal</th>
                <th width="9%" class="th-awal-sub">NS</th>
                <th width="10%" class="th-awal-sub">Counter Mesin Awal</th>
                <th width="6%" class="th-awal-sub">Volt</th>
                <th width="9%" class="th-new-sub">Tipe Mesin Baru</th>
                <th width="9%" class="th-new-sub">NS</th>
                <th width="10%" class="th-new-sub">Counter</th>
                <th width="6%" class="th-new-sub">Volt</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                @php
                    $voltase = !empty($row->volt_mesin) ? trim($row->volt_mesin) . ' V' : '220 V';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : now()->format('d/m/Y') }}
                    </td>
                    <td>
                        <strong>{{ strtoupper($row->nama_customer ?? 'Umum') }}</strong>
                        <br>
                        <small style="color:#555;">{{ $row->kota ?? '' }}</small>
                    </td>

                    {{-- Unit Awal --}}
                    <td class="td-awal">{{ $row->tipe_lama ?? '-' }}</td>
                    <td class="td-awal font-bold">{{ $row->sn_lama ?? '-' }}</td>
                    <td class="td-awal">
                        BW: {{ number_format($row->counter_bw_old ?? 0) }}<br>
                        CL: {{ number_format($row->counter_color_old ?? 0) }}
                    </td>
                    <td class="td-awal text-center font-bold" style="color:#1e40af;">{{ $voltase }}</td>

                    {{-- Unit Baru --}}
                    <td class="td-new">{{ $row->tipe_baru ?? '-' }}</td>
                    <td class="td-new font-bold">{{ $row->sn_baru ?? '-' }}</td>
                    <td class="td-new">
                        BW: {{ number_format($row->counter_bw_new ?? 0) }}<br>
                        CL: {{ number_format($row->counter_color_new ?? 0) }}
                    </td>
                    <td class="td-new text-center font-bold" style="color:#16a34a;">{{ $voltase }}</td>

                    <td>{{ $row->alasan_ganti ?? 'Rolling Unit' }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="12">
                        Belum ada riwayat tukar guling pada periode {{ $namaBulan }} {{ $tahun }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-box">
        <p>Indramayu, {{ now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <strong>( ________________ )</strong>
        <p style="font-weight:bold;">Admin Operasional</p>
    </div>

</body>

</html>
