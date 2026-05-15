<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Penyerahan Unit</title>
    <style>
        body { font-family: sans-serif; margin: 30px; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .ttd-container { margin-top: 50px; width: 100%; }
        .ttd-box { width: 33%; float: left; text-align: center; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="background: blue; color: white; padding: 10px; cursor: pointer;">🖨️ CETAK SEKARANG</button>
        <hr>
    </div>

    <div class="header">
        <h2 style="margin:0">PT DINAMIKA GLOBAL GEMILANG</h2>
        <p style="margin:5px">SURAT JALAN PENYERAHAN UNIT MESIN (RR)</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Customer</td>
            <td>: <strong>{{ $log->customer?->nama_customer }}</strong></td>
            <td width="15%">Tanggal</td>
            <td>: {{ $log->tanggal->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Teknisi</td>
            <td>: {{ $log->technician?->nama_technician }}</td>
            <td>Tipe</td>
            <td>: <strong>{{ $log->tipe_kunjungan }}</strong></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Deskripsi Unit</th>
                <th>Model</th>
                <th>Serial Number (SN)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Mesin Fotokopi (Unit Ganti)</td>
                <td>{{ $log->machine?->tipe_model }}</td>
                <td><strong>{{ $log->machine?->serial_number }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top:20px">
        <p><strong>Keterangan / Kerusakan Awal:</strong><br>{{ $log->kerusakan }}</p>
    </div>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Penerima,</p><br><br><p>( .................... )</p>
        </div>
        <div class="ttd-box">
            <p>Teknisi,</p><br><br><p><strong>( {{ $log->technician?->nama_technician }} )</strong></p>
        </div>
        <div class="ttd-box">
            <p>Hormat Kami,</p><br><br><p>( Admin DGG )</p>
        </div>
    </div>
</body>
</html>