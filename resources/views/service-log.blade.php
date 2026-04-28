<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Report - {{ $record->machine->serial_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .content-table { width: 100%; border: 1px solid #000; border-collapse: collapse; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig { text-align: center; width: 200px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="background: yellow; padding: 10px; margin-bottom: 20px;">
        <button onclick="window.print()">KLIK UNTUK CETAK</button>
        <button onclick="window.history.back()">KEMBALI</button>
    </div>

    <div class="header">
        <h2>LAPORAN SERVICE MESIN FOTOCOPY (DGG)</h2>
        <p>Jl. Pulasaren No. XX, Cirebon - WhatsApp: 0812-XXXX-XXXX</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Customer</strong></td>
            <td width="35%">: {{ $record->machine->customer->nama_customer ?? '-' }}</td>
            <td width="15%"><strong>Tgl Servis</strong></td>
            <td width="35%">: {{ $record->tanggal->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Model Mesin</strong></td>
            <td>: {{ $record->machine->model_mesin }}</td>
            <td><strong>Jam Kerja</strong></td>
            <td>: {{ $record->jam_mulai }} - {{ $record->jam_selesai }}</td>
        </tr>
        <tr>
            <td><strong>Serial Number</strong></td>
            <td>: {{ $record->machine->serial_number }}</td>
            <td><strong>Tipe Kunjungan</strong></td>
            <td>: {{ $record->tipe_kunjungan }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr style="background: #eee;">
                <th>Kategori Meteran</th>
                <th>Lalu</th>
                <th>Sekarang</th>
                <th>Total Pakai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hitam Putih (BW)</td>
                <td>{{ number_format($record->bw_lalu) }}</td>
                <td>{{ number_format($record->counter_bw) }}</td>
                <td><strong>{{ number_format($record->usage_bw) }}</strong></td>
            </tr>
            <tr>
                <td>Warna (Color)</td>
                <td>{{ number_format($record->color_lalu) }}</td>
                <td>{{ number_format($record->counter_color) }}</td>
                <td><strong>{{ number_format($record->usage_color) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Analisa Kerusakan:</strong><br>{{ $record->kerusakan }}</p>
        <p><strong>Tindakan Perbaikan:</strong><br>{{ $record->perbaikan }}</p>
    </div>

    <div class="footer">
        <div class="sig">
            <p>Customer,</p>
            <br><br><br>
            <p>( ........................ )</p>
        </div>
        <div class="sig">
            <p>Teknisi Utama,</p>
            <br><br><br>
            <p><strong>{{ $record->technician->nama_technician }}</strong></p>
        </div>
    </div>
</body>
</html>