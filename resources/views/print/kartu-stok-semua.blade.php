<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Stok Semua Teknisi</title>
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
            color: #1d1919;
            margin-bottom: 20px;
        }

        .teknisi-block {
            margin-bottom: 28px;
            page-break-inside: avoid;
        }

        .teknisi-name {
            background: #cccac9;
            color: rgb(12, 12, 12);
            padding: 6px 10px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e8f0fe;
            color: #1e3a5f;
            padding: 7px 8px;
            text-align: left;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            background: #fef3c7;
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
    <h2>Kartu Stok Semua Teknisi</h2>
    <p class="sub">Dicetak: {{ now()->format('d M Y H:i') }}</p>

    @forelse($data as $item)
        <div class="teknisi-block">
            <div class="teknisi-name">{{ $item['teknisi']->nama_technician }}</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sparepart</th>
                        <th>Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item['stocks'] as $i => $stock)
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
                        <td colspan="2">Total</td>
                        <td>{{ $item['total'] }} pcs</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p style="text-align:center;color:#999;">Tidak ada data stok teknisi.</p>
    @endforelse

    <div class="footer">Sistem DGG &copy; {{ date('Y') }}</div>

    <script>
        window.onload = () => window.print();
    </script>
</body>

</html>
