<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Matrix Pemakaian Sparepart - {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
        }

        .page {
            width: 297mm;
            min-height: 210mm;
            padding: 10mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 12px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
            font-size: 10px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #333;
            padding: 3px 4px;
            background: #d0d0d0;
            font-weight: bold;
            text-align: center;
        }

        td {
            border: 1px solid #333;
            padding: 3px 4px;
            text-align: center;
        }

        td.nama {
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
        }

        .total-col {
            background: #fef3c7;
            font-weight: bold;
        }

        .no-print {
            width: 297mm;
            margin: 10px auto;
            padding: 10px 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Matrix Pemakaian Sparepart ke Customer per Bulan — Tahun {{ $tahun }}</h2>
            <p>Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
        <hr>

        <table>
            <thead>
                <tr>
                    <th style="width:160px; text-align:left;">Nama Sparepart</th>
                    @foreach ($bulanLabel as $label)
                        <th style="width:40px;">{{ $label }}</th>
                    @endforeach
                    <th class="total-col" style="width:50px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matrix as $row)
                    <tr>
                        <td class="nama">{{ $row['sparepart']->nama_sparepart }}</td>
                        @foreach (range(1, 12) as $bulan)
                            <td>{{ $row['bulanan'][$bulan] ?: '-' }}</td>
                        @endforeach
                        <td class="total-col">{{ $row['total'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" style="padding:20px; color:#888;">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL SEMUA PART</td>
                    @foreach (range(1, 12) as $bulan)
                        <td>{{ $totalPerBulan[$bulan] }}</td>
                    @endforeach
                    <td class="total-col">{{ array_sum($totalPerBulan) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</body>

</html>
