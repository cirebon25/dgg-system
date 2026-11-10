<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Status Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 20px;
            text-transform: uppercase;
        }

        button {
            text-transform: none;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 6px 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 6px 8px;
            border: 1px solid #888;
            text-align: left;
            vertical-align: middle;
        }

        table th {
            background-color: #eee;
            text-align: center;
            font-size: 10.5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Kolom yang tidak boleh turun baris */
        .nw {
            white-space: nowrap;
        }

        /* Lebar kolom (muat s/d Rp 100.000.000) */
        .w-no {
            width: 30px;
        }

        .w-nominal {
            width: 115px;
        }

        .total-row td {
            background-color: #eee;
            font-weight: bold;
        }

        .badge-belum {
            color: #d9534f;
            font-weight: bold;
        }

        .badge-sudah {
            color: #3d9b3d;
            font-weight: bold;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
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
        <p>PERIODE: {{ $periode ?? 'SEMUA' }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-no">No</th>
                <th class="nw">No. Invoice</th>
                <th>Nama Customer</th>
                <th class="nw">Tanggal</th>
                <th class="nw w-nominal">Nominal</th>
                <th class="nw">Pengirim</th>
                <th class="nw">Penerima</th>
                <th class="nw">Status</th>
                <th class="nw">Tgl & Waktu Diterima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="nw">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->nama_customer ?? 'Belum ada Customer' }}</td>
                    <td class="nw text-center">
                        {{ $invoice->tanggal ? \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="nw text-left">Rp {{ number_format($invoice->nominal ?? 0, 0, ',', '.') }}</td>
                    <td class="nw">{{ $invoice->nama_pengirim ?: '-' }}</td>
                    <td class="nw">{{ $invoice->nama_penerima ?: '-' }}</td>
                    <td class="nw text-center">
                        @if ($invoice->is_received)
                            <span class="badge-sudah">Sudah</span>
                        @else
                            <span class="badge-belum">Belum</span>
                        @endif
                    </td>
                    <td class="nw text-center">
                        {{ $invoice->received_at ? \Carbon\Carbon::parse($invoice->received_at)->format('d/m/Y H:i') : 'Belum' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data invoice.</td>
                </tr>
            @endforelse

            @if ($invoices->isNotEmpty())
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL NOMINAL</td>
                    <td class="nw text-right">Rp {{ number_format($invoices->sum('nominal'), 0, ',', '.') }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
        </tbody>
    </table>

</body>

</html>
