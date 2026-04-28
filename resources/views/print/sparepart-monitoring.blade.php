<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Umur Sparepart - {{ $machine->serial_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .text-danger { color: red; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>MONITORING UMUR PAKAI SPAREPART</h2>
        <p><strong>Customer:</strong> {{ $machine->deployment->customer->nama_customer ?? '-' }} | 
           <strong>SN:</strong> {{ $machine->serial_number }} | 
           <strong>Model:</strong> {{ $machine->model_mesin }}</p>
        <p><strong>Counter Mesin Saat Ini:</strong> BW: {{ number_format($counterSekarangBW) }} | CL: {{ number_format($counterSekarangCL) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Sparepart</th>
                <th>Tanggal Pasang</th>
                <th>Counter Awal (Pasang)</th>
                <th>Counter Sekarang</th>
                <th>Total Pemakaian (Klik)</th>
                <th>Estimasi Umur</th>
                <th>Sisa Umur (%)</th>
                <th>Lokasi Saat Pasang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partHistories as $history)
                @php
                    $awal = $history->serviceLog->counter_bw; // Contoh fokus ke BW dulu
                    $pakai = $counterSekarangBW - $awal;
                    $target = $history->sparepart->target_umur ?? 150000; // Default 150rb jika belum diisi
                    $sisaPersen = ($target > 0) ? round((($target - $pakai) / $target) * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $history->sparepart->nama_sparepart }}</td>
                    <td>{{ \Carbon\Carbon::parse($history->serviceLog->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ number_format($awal) }}</td>
                    <td>{{ number_format($counterSekarangBW) }}</td>
                    <td><strong>{{ number_format($pakai) }} Lembar</strong></td>
                    <td>{{ number_format($target) }}</td>
                    <td class="{{ $sisaPersen < 15 ? 'text-danger' : '' }}">
                        {{ $sisaPersen }}%
                    </td>
                    <td>{{ $history->serviceLog->machine->deployment->customer->nama_customer ?? 'Workshop/Internal' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>