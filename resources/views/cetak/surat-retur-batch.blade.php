<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan Retur</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px 8px; }
        th { background: #f0f0f0; text-align: center; }
        .header { text-align: center; margin-bottom: 15px; }
        .info { margin-bottom: 10px; }
        .info td { border: none; padding: 2px 5px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>SURAT JALAN RETUR MESIN</h2>
        <p>Gudang Cirebon → Gudang Bandung</p>
    </div>

    <table class="info">
        <tr>
            <td width="120"><b>Tanggal Kirim</b></td>
            <td>: {{ \Carbon\Carbon::parse($returns->first()->tanggal_retur)->format('d/m/Y') }}</td>
            <td width="120"><b>Dikirim Oleh</b></td>
            <td>: {{ $returns->first()->dikirim_oleh ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Kondisi</b></td>
            <td>: {{ $returns->first()->kondisi_saat_retur }}</td>
            <td><b>Jumlah Mesin</b></td>
            <td>: {{ $returns->count() }} unit</td>
        </tr>
        @if($returns->first()->keterangan_kerusakan)
        <tr>
            <td><b>Keterangan</b></td>
            <td colspan="3">: {{ $returns->first()->keterangan_kerusakan }}</td>
        </tr>
        @endif
    </table>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Serial Number</th>
                <th>Tipe / Model</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returns as $i => $return)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td><b>{{ $return->machine->serial_number }}</b></td>
                <td>{{ $return->machine->tipe_model }}</td>
                <td style="text-align:center">{{ $return->machine->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <table>
        <tr>
            <td style="text-align:center; border:1px solid #000; padding:10px;">
                <b>Pengirim</b><br><br><br><br>
                ({{ $returns->first()->dikirim_oleh ?? '...................' }})
            </td>
            <td style="text-align:center; border:1px solid #000; padding:10px;">
                <b>Penerima Bandung</b><br><br><br><br>
                (......................)
            </td>
        </tr>
    </table>

</body>
</html>