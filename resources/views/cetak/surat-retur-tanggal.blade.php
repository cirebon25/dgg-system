<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Jalan Retur - {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 8px;
            /* ✅ fix: 2in → 2px */
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .info-table td {
            border: none;
            padding: 2px 5px;
        }

        .ttd-table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #000;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>SURAT JALAN RETUR MESIN</h2>
        <p>Gudang Cirebon → Gudang Bandung</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="130"><b>Tanggal Kirim</b></td>
            <td>: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</td>
            <td width="130"><b>Dikirim Oleh</b></td>
            <td>: {{ $returns->first()->dikirim_oleh ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Dari</b></td>
            <td>: Gudang Cirebon</td>
            <td><b>Ke</b></td>
            <td>: Gudang Bandung</td>
        </tr>
        <tr>
            <td><b>Jumlah Mesin</b></td>
            <td>: <b>{{ $returns->count() }} unit</b></td>
            <td><b>Kondisi</b></td>
            {{-- <td>: {{ $returns->pluck('kondisi_saat_retur')->unique()->join(', ') }}</td> --}}
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Serial Number</th>
                <th>Tipe / Model</th>
                <th>Kondisi</th>
                <th>Keterangan Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($returns as $i => $return)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td><b>{{ $return->machine->serial_number }}</b></td>
                    <td>{{ $return->machine->tipe_model }}</td>
                    <td style="text-align:center">{{ '-' }}</td>
                    <td>{{ $return->keterangan_kerusakan ?? '-' }}</td>
                </tr>
            @endforeach

            {{-- Sisa baris kosong agar selalu 10 baris --}}
            @for ($i = $returns->count(); $i < 10; $i++)
                <tr>
                    <td style="text-align:center; color:#ccc;">{{ $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <br>
    <table class="ttd-table">
        <tr>
            <td width="50%">
                <b>Pengirim</b><br><br><br><br>
                ({{ $returns->first()->dikirim_oleh ?? '...................' }})
            </td>
            <td width="50%">
                <b>Penerima Bandung</b><br><br><br><br>
                (....................)
            </td>
        </tr>
    </table>

</body>

</html>
