<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Rincian Harian {{ $dari->format('d/m/Y') }} - {{ $sampai->format('d/m/Y') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            margin: 0;
        }

        .noprint {
            padding: 8px;
        }

        .page {
            page-break-after: always;
            padding: 6px 10px;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .judul {
            border: 1px solid #000;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            padding: 2px 0;
            margin-bottom: 4px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        table.info td {
            padding: 1px 4px;
        }

        table.info td.r {
            text-align: right;
        }

        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 2px 0 4px;
        }

        table.emp {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        table.emp td {
            padding: 0 4px;
            font-weight: bold;
            font-style: italic;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 1px 3px;
            white-space: nowrap;
        }

        table.data th {
            text-align: center;
            font-weight: bold;
            line-height: 1.1;
        }

        table.data td.c {
            text-align: center;
        }

        td.cyan {
            background: #00ffff;
        }

        .cb {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1px solid #000;
            line-height: 8px;
            font-size: 9px;
            text-align: center;
            vertical-align: middle;
        }

        .cb.on::after {
            content: "\2713";
        }

        .footer {
            margin-top: 6px;
            font-size: 9px;
        }

        .footer span {
            margin-right: 28px;
        }

        @media print {
            .noprint {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="noprint">
        <button onclick="window.print()">Cetak</button>
    </div>

    @foreach ($pages as $i => $blocks)
        <section class="page">
            <div class="judul">LAPORAN RINCIAN HARIAN</div>

            <table class="info">
                <tr>
                    <td>Nama Perusahaan : <b>{{ $perusahaan }}</b></td>
                    <td>Urut Berdasarkan : <b>PIN, Tanggal</b></td>
                    <td class="r">Tgl. Periode : <b>{{ $dari->format('d/m/Y') }} &nbsp;s/d&nbsp;
                            {{ $sampai->format('d/m/Y') }}</b></td>
                </tr>
            </table>
            <hr>

            @foreach ($blocks as $b)
                @php $emp = $b['emp']; @endphp

                <table class="emp">
                    <tr>
                        <td style="width:33%">PIN : {{ $emp->pin }}</td>
                        <td style="width:34%">Nama : {{ $emp->name }}</td>
                        <td style="width:33%">Departemen : {{ $emp->departemen }}</td>
                    </tr>
                    <tr>
                        <td>NIP : {{ $emp->nip }}</td>
                        <td>Jabatan : {{ $emp->jabatan }}</td>
                        <td>Kantor : {{ $emp->kantor }}</td>
                    </tr>
                </table>

                <table class="data">
                    <thead>
                        <tr>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Nama Shift</th>
                            <th rowspan="2">Jam<br>Masuk</th>
                            <th rowspan="2">Scan<br>Masuk</th>
                            <th rowspan="2">Datang<br>Terlambat</th>
                            <th rowspan="2">Jam<br>Keluar</th>
                            <th rowspan="2">Scan<br>Keluar</th>
                            <th rowspan="2">Pulang<br>Awal</th>
                            <th rowspan="2">Durasi<br>Kerja</th>
                            <th colspan="2">Istirahat</th>
                            <th rowspan="2">Lembur<br>Akhir</th>
                            <th colspan="2">Libur</th>
                            <th rowspan="2">Shift<br>Lembur</th>
                            <th rowspan="2" style="width:12%">Keterangan</th>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <th>Lebih</th>
                            <th>Umum</th>
                            <th>Rutin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($b['rows'] as $row)
                            <tr>
                                <td>{{ $row['tanggal'] }}</td>
                                <td>{{ $row['shift'] }}</td>
                                <td class="c">{{ $row['jam_masuk'] }}</td>
                                <td class="c {{ $row['cyan_masuk'] ? 'cyan' : '' }}">{{ $row['scan_masuk'] }}</td>
                                <td class="c">{{ $row['terlambat'] }}</td>
                                <td class="c">{{ $row['jam_keluar'] }}</td>
                                <td class="c {{ $row['cyan_keluar'] ? 'cyan' : '' }}">{{ $row['scan_keluar'] }}</td>
                                <td class="c">{{ $row['pulang_awal'] }}</td>
                                <td class="c">{{ $row['durasi'] }}</td>
                                <td class="c">{{ $row['ist_durasi'] }}</td>
                                <td class="c">{{ $row['ist_lebih'] }}</td>
                                <td class="c">{{ $row['lembur_akhir'] }}</td>
                                <td class="c"><span class="cb {{ $row['libur_umum'] ? 'on' : '' }}"></span></td>
                                <td class="c"><span class="cb {{ $row['libur_rutin'] ? 'on' : '' }}"></span></td>
                                <td class="c"><span class="cb {{ $row['shift_lembur'] ? 'on' : '' }}"></span></td>
                                <td>{{ $row['keterangan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach

            <div class="footer">
                <span>Halaman : {{ $i + 1 }}</span>
                <span>dari : {{ $pages->count() }}</span>
                <span>Tgl. Cetak : {{ $dicetak->format('d/m/Y H:i:s') }}</span>
                <span>Oleh : {{ $oleh }}</span>
            </div>
        </section>
    @endforeach
</body>

</html>
