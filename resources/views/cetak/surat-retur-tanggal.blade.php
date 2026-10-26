<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Jalan Retur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .info td {
            border: none;
            padding: 2px 5px;
        }

        .empty td {
            color: #aaa;
            height: 18px;
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
        $minRows = 10;
    @endphp

    <div class="header">
        <h2>SURAT JALAN RETUR MESIN</h2>
        <p>Gudang Cirebon → Gudang Bandung</p>
    </div>

    <table class="info">
        <tr>
            <td width="110"><b>Tanggal Kirim</b></td>
            <td>: {{ \Carbon\Carbon::parse($first->tanggal_retur)->format('d/m/Y') }}</td>
            <td width="110"><b>Dikirim Oleh</b></td>
            <td>: {{ $first->dikirim_oleh ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Dari</b></td>
            <td>: Gudang Cirebon</td>
            <td><b>Ke</b></td>
            <td>: Gudang Bandung</td>
        </tr>
        <tr>
            <td><b>Jumlah Mesin</b></td>
            <td>: {{ $returns->count() }} unit</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="150">Serial Number</th>
                <th width="110">Tipe / Model</th>
                <th width="90">Kondisi</th>
                <th>Keterangan Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($returns as $i => $return)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td><b>{{ $return->machine->serial_number }}</b></td>
                    <td>{{ $return->machine->tipe_model }}</td>
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

    <br>
    <table>
        <tr>
            <td style="text-align:center; padding:10px; width:50%;">
                <b>Pengirim</b><br><br><br><br>
                ({{ $first->dikirim_oleh ?? '...................' }})
            </td>
            <td style="text-align:center; padding:10px; width:50%;">
                <b>Penerima Bandung</b><br><br><br><br>
                (......................)
            </td>
        </tr>
    </table>

</body>

</html>
