<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Stok - {{ $teknisi->nama_technician }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        p.sub {
            text-align: center;
            color: #555;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e3a5f;
            color: white;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 7px 8px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        .total-row {
            font-weight: bold;
            background: #e8f0fe;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 24px;
            text-align: right;
            color: #888;
            font-size: 11px;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <h2>Kartu Stok Teknisi</h2>
    <p class="sub">{{ $teknisi->nama_technician }} &mdash; Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Sisa Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $i => $stock)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $stock->sparepart->nama_sparepart ?? '-' }}</td>
                    <td>
                        @php
                            $class =
                                $stock->jumlah > 5
                                    ? 'badge-success'
                                    : ($stock->jumlah > 0
                                        ? 'badge-warning'
                                        : 'badge-danger');
                        @endphp
                        <span class="badge {{ $class }}">{{ $stock->jumlah }} pcs</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;color:#999;">Tidak ada stok</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">Total Semua Item</td>
                <td>{{ $total }} pcs</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">Sistem DGG &copy; {{ date('Y') }}</div>

    <script>
        window.onload = () => window.print();
    </script>
</body>

</html>
