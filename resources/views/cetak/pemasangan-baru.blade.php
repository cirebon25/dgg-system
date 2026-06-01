<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pemasangan Baru - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0 0 4px 0;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h3 {
            margin: 0;
            font-size: 12px;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            background-color: #a59f9f;
            color: #000000;
        }

        th {
            background-color: #af9f9f;
            color: #000000;
            padding: 10px 5px;
            border: 1px solid #000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #666;
            vertical-align: middle;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .empty-row td {
            text-align: center;
            padding: 20px;
            color: #666;
            font-weight: bold;
        }

        ul.part-list {
            margin: 0;
            padding-left: 14px;
        }

        ul.part-list li {
            margin-bottom: 2px;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
            overflow: hidden;
        }

        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        .ttd-box p {
            margin: 4px 0;
        }

        .ttd-space {
            margin-bottom: 60px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>Daftar Customer Pasang Baru DGG Cirebon</h2>
        <h3>Periode: {{ $namaBulan }} {{ $tahun }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Pasang</th>
                <th>Nama Customer</th>
                <th>Tipe Model</th>
                <th>No Seri</th>
                <th>Volt</th>
                <th>Ctr Awal</th>
                <th>Teknisi</th>
                <th>Part</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}
                    </td>
                    <td>{{ $row->customer?->nama_customer ?? '-' }}</td>
                    <td>{{ $row->machine->tipe_model ?? '-' }}</td>
                    <td class="text-center"><b>{{ $row->machine->serial_number ?? '-' }}</b></td>
                    <td class="text-center">{{ $row->volt }} V</td>
                    <td class="text-center">
                        BW: {{ number_format($row->counter_bw) }}<br>
                        CL: {{ number_format($row->counter_color) }}
                    </td>
                    <td>{{ $row->technician->nama_technician ?? '-' }}</td>
                    <td>
                        @php
                            $parts = $sparepartPerDeployment->get($row->id, collect());
                        @endphp
                        @if ($parts->isNotEmpty())
                            <ul class="part-list">
                                @foreach ($parts as $p)
                                    <li><b>{{ strtoupper($p->nama_sparepart) }}</b> ({{ $p->jumlah }} Pcs)</li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="10">
                        Tidak ada data pemasangan baru pada periode {{ $namaBulan }} {{ $tahun }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd-box">
            <p>Indramayu, {{ now()->translatedFormat('d F Y') }}</p>
            <p class="ttd-space">Admin Operasional,</p>
            <strong>( _________________________ )</strong>
        </div>
    </div>

</body>

</html>
