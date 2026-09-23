<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap MRC - {{ $periode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2,
        h4 {
            text-align: center;
            margin: 0;
        }

        .header {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #999;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer;">Cetak Halaman</button>
    </div>

    <div class="header">
        <h2>REKAPITULASI MRC & BILLING MESIN</h2>
        <h4>Periode: {{ $periode }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>SN Mesin</th>
                <th>Customer</th>
                <th>Model</th>
                <th>Ctr BW Lalu</th>
                <th>Ctr BW Ini</th>
                <th>Usage BW</th>
                <th>Ctr Color Lalu</th>
                <th>Ctr Color Ini</th>
                <th>Usage Color</th>
                <th>Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $log->machine->serial_number ?? '-' }}</td>
                    <td class="text-left">{{ $log->customer->nama_customer ?? '-' }}</td>
                    <td>{{ $log->machine->tipe_model ?? '-' }}</td>
                    <td>{{ number_format($log->counter_bw_lalu ?? 0) }}</td>
                    <td>{{ number_format($log->counter_bw ?? 0) }}</td>
                    <td><strong>{{ number_format($log->usage_bw ?? 0) }}</strong></td>
                    <td>{{ $log->counter_color_lalu ? number_format($log->counter_color_lalu) : '-' }}</td>
                    <td>{{ $log->counter_color ? number_format($log->counter_color) : '-' }}</td>
                    <td><strong>{{ $log->usage_color ? number_format($log->usage_color) : '-' }}</strong></td>
                    <td>{{ $log->technician->nama_technician ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 15px;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
