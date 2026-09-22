<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Histori Stok Teknisi - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .technician-section {
            margin-top: 25px;
        }

        .technician-title {
            font-size: 14px;
            font-weight: bold;
            background: #e2e8f0;
            padding: 6px 10px;
            margin-bottom: 5px;
            border-left: 4px solid #3b82f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-size: 11px;
        }

        .summary-row {
            font-weight: bold;
            background-color: #f8fafc;
        }
    </style>
</head>

<body onload="window.print()">

    <h2 class="text-center" style="margin-bottom: 2px;">Laporan Histori Stok Teknisi</h2>
    <p class="text-center" style="margin-top: 0; color: #64748b;">Periode: {{ $month }} / {{ $year }}</p>

    @forelse($groupedHistories as $technicianId => $items)
        @php
            $technicianName = $items->first()->technician->nama_technician ?? 'Tanpa Teknisi';
            $totalMasuk = $items->sum('masuk');
            $totalKeluar = $items->sum('keluar');
        @endphp

        <div class="technician-section">
            <div class="technician-title">Teknisi: {{ $technicianName }}</div>

            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Tanggal & Waktu</th>
                        <th width="25%">Nama Part</th>
                        <th width="10%" class="text-center">Masuk (+)</th>
                        <th width="10%" class="text-center">Keluar (-)</th>
                        <th width="12%" class="text-center">Sisa Di Tas</th>
                        <th width="20%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $history)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $history->sparepart->nama_sparepart ?? '-' }}</td>
                            <td class="text-center">{{ $history->masuk ?: 0 }}</td>
                            <td class="text-center">{{ $history->keluar ?: 0 }}</td>
                            <td class="text-center"><strong>{{ $history->saldo_akhir }}</strong></td>
                            <td>{{ $history->keterangan }}</td>
                        </tr>
                    @endforeach

                    <!-- Baris Rekap Per Teknisi -->
                    <tr class="summary-row">
                        <td colspan="3" class="text-right">Total Mutasi Masuk & Keluar:</td>
                        <td class="text-center" style="color: green;">+{{ $totalMasuk }}</td>
                        <td class="text-center" style="color: red;">-{{ $totalKeluar }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p class="text-center" style="margin-top: 40px;">Tidak ada data histori stok pada periode ini.</p>
    @endforelse

</body>

</html>
