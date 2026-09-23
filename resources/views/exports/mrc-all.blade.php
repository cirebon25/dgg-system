<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Semua Data Kontrak MRC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-aktif {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-nonaktif {
            background-color: #fee2e2;
            color: #991b1b;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h2>Laporan Data Kontrak & Pemakaian MRC</h2>
        <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>SN Mesin & Tipe</th>
                <th>Customer</th>
                <th class="text-right">Harga Sewa</th>
                <th class="text-center">Free BW / Harga</th>
                <th class="text-center">Free Color / Harga</th>
                <th class="text-center">Status</th>
                <th class="text-center">Mulai Kontrak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts->sortByDesc('harga_sewa') as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->machine?->serial_number ?? '-' }}</strong><br>
                        <small style="color: #666;">{{ $item->machine?->tipe_model ?? '-' }}</small>
                    </td>
                    <td>{{ $item->customer?->nama_customer ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</td>
                    <td class="text-center">
                        {{ number_format($item->free_bw) }} lbr<br>
                        <small style="color: #666;">(Rp {{ number_format($item->harga_bw, 0, ',', '.') }}/lbr)</small>
                    </td>
                    <td class="text-center">
                        {{ number_format($item->free_color) }} lbr<br>
                        <small style="color: #666;">(Rp
                            {{ number_format($item->harga_color, 0, ',', '.') }}/lbr)</small>
                    </td>
                    <td class="text-center">
                        @if ($item->aktif)
                            <span class="badge badge-aktif">Aktif</span>
                        @else
                            <span class="badge badge-nonaktif">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #777;">Tidak ada data kontrak
                        MRC.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
