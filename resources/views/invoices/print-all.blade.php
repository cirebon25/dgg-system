<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Status Invoice</title>
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
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0 0 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 8px 10px;
            border: 1px solid #999;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .badge-belum {
            color: #d9534f;
            font-weight: bold;
        }

        .badge-sudah {
            color: #5cb85c;
            font-weight: bold;
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

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 8px 15px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Cetak
            Ulang</button>
    </div>

    <div class="header">
        <h2>REKAP STATUS INVOICE KONSUMEN</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th>No. Invoice</th>
                <th>Nama Customer</th>
                <th>Tanggal</th>
                <th class="text-center">Status Diterima</th>
                <th>Tgl & Waktu Diterima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <!-- Memastikan nama customer terpanggil dengan aman -->
                    <td>{{ $invoice->customer->nama_customer ?? 'Belum ada Customer' }}</td>
                    <td>{{ $invoice->tanggal ? \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        @if ($invoice->is_received)
                            <span class="badge-sudah">Sudah</span>
                        @else
                            <span class="badge-belum">Belum</span>
                        @endif
                    </td>
                    <td>
                        {{ $invoice->received_at ? \Carbon\Carbon::parse($invoice->received_at)->format('d/m/Y H:i') : 'Belum' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data invoice.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
