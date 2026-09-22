<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .header h2 {
            margin: 0;
            color: #333;
        }

        .customer-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="invoice-box">
        <div class="header">
            <div>
                <h2>INVOICE</h2>
                <p>No: <strong>{{ $invoice->invoice_number }}</strong></p>
            </div>
            <div>
                <p>Tanggal: {{ \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') }}</p>
                <p>Status: <strong>{{ $invoice->is_received ? 'Sudah Diterima' : 'Belum Diterima' }}</strong></p>
            </div>
        </div>

        <div class="customer-info">
            <strong>Kepada Yth:</strong><br>
            {{ $invoice->customer->nama_customer ?? '-' }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Keterangan / Layanan</th>
                    <th style="width: 100px; text-align: center;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tagihan / Layanan MRC</td>
                    <td style="text-align: center;">1</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div>
                <p class="no-print" style="color: #666; font-size: 12px;">Halaman ini otomatis mencetak...</p>
                <button class="no-print" onclick="window.print()"
                    style="padding: 10px 20px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Cetak
                    Ulang</button>
            </div>
            <div class="signature">
                <p>Hormat Kami,</p>
                <br><br><br>
                <p><strong>Admin</strong></p>
            </div>
        </div>
    </div>

</body>

</html>
