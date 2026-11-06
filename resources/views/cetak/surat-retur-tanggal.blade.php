<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Jalan Retur</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 12pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info {
            margin-bottom: 8px;
        }

        .info td {
            border: none;
            padding: 1px 4px;
            vertical-align: top;
        }

        .data th,
        .data td {
            border: 1px solid #000;
            padding: 3px 6px;
            vertical-align: top;
            font-size: 12pt;
        }

        .data th {
            text-align: center;
        }

        .data tr {
            page-break-inside: avoid;
        }

        .empty td {
            height: 20px;
        }

        .ttd {
            margin-top: 12px;
            page-break-inside: avoid;
        }

        .ttd td {
            border: none;
            text-align: center;
            width: 50%;
            padding: 0;
        }

        .ttd .space {
            height: 50px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    @php
        $first = $returns->first();
        $minRows = 5;
    @endphp

    <div class="header">
        <h2>Surat Jalan Retur Mesin</h2>
        <p>Gudang Cirebon &rarr; Gudang Bandung</p>
    </div>

    <table class="info">
        <tr>
            <td width="110">Tanggal Kirim</td>
            <td>: {{ \Carbon\Carbon::parse($first->tanggal_retur)->format('d/m/Y') }}</td>
            <td width="110">Dikirim Oleh</td>
            <td>: {{ $first->dikirim_oleh ?? '-' }}</td>
        </tr>
        <tr>
            <td>Dari</td>
            <td>: Gudang Cirebon</td>
            <td>Ke</td>
            <td>: Gudang Bandung</td>
        </tr>
        <tr>
            <td>Jumlah Mesin</td>
            <td>: {{ $returns->count() }} unit</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="35">No</th>
                <th width="130">Serial Number</th>
                <th width="110">Tipe / Model</th>
                <th width="110">Kondisi</th>
                <th>Keterangan Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($returns as $i => $return)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $return->machine?->serial_number ?? '-' }}</td>
                    <td>{{ $return->machine?->tipe_model ?? '-' }}</td>
                    <td style="text-align:center">{{ $return->kondisi_saat_retur ?? '-' }}</td>
                    <td>{{ $return->keterangan_kerusakan ?: '-' }}</td>
                </tr>
            @endforeach

            @for ($n = $returns->count() + 1; $n <= $minRows; $n++)
                <tr class="empty">
                    <td style="text-align:center">{{ $n }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td>Pengirim</td>
            <td>Penerima Bandung</td>
        </tr>
        <tr>
            <td class="space"></td>
            <td class="space"></td>
        </tr>
        <tr>
            <td>( {{ $first->dikirim_oleh ?? '....................' }} )</td>
            <td>( .................... )</td>
        </tr>
    </table>

</body>

</html>
