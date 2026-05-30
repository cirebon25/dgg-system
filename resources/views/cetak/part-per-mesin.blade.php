<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riwayat Ganti Part - {{ $machine->serial_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
        }

        .header p {
            font-size: 11px;
            color: #444;
            margin-top: 3px;
        }

        .info-box {
            display: flex;
            gap: 20px;
            margin-bottom: 14px;
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box span {
            font-weight: bold;
        }

        .part-section {
            margin-bottom: 18px;
        }

        .part-title {
            background: #333;
            color: #fff;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 11px;
            border-radius: 3px 3px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        th {
            background: #555;
            color: #fff;
            padding: 5px 8px;
            text-align: left;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .badge-normal {
            color: #15803d;
            font-weight: bold;
        }

        .badge-warning {
            color: #b45309;
            font-weight: bold;
        }

        .badge-danger {
            color: #b91c1c;
            font-weight: bold;
        }

        .counter-box {
            margin-top: 10px;
            background: #fffbeb;
            border: 1px solid #f59e0b;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 11px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>RIWAYAT GANTI SPAREPART PER MESIN</h2>
        <p>DGG System &mdash; Dicetak {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <div><span>Serial Number:</span> {{ $machine->serial_number }}</div>
        <div><span>Tipe:</span> {{ $machine->tipe_model ?? '-' }}</div>
        <div><span>Lokasi:</span> {{ $machine->customer?->nama_customer ?? 'Gudang DGG' }}</div>
        <div><span>Counter Terakhir:</span> {{ number_format($counterTerakhir) }} Lbr</div>
    </div>

    @foreach ($data as $sparepartId => $rows)
        @php $namaSparepart = $rows->first()->sparepart?->nama_sparepart ?? '-'; @endphp
        <div class="part-section">
            <div class="part-title">{{ strtoupper($namaSparepart) }} &mdash; {{ $rows->count() }}x diganti</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Ganti</th>
                        <th>Counter Sebelumnya</th>
                        <th>Counter Saat Ganti</th>
                        <th>Selisih Pemakaian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        @php
                            $class =
                                $row->selisih >= 80000
                                    ? 'badge-danger'
                                    : ($row->selisih >= 50000
                                        ? 'badge-warning'
                                        : 'badge-normal');
                            $status =
                                $row->selisih >= 80000 ? 'Berat' : ($row->selisih >= 50000 ? 'Perhatian' : 'Normal');
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ number_format($row->counter_sebelumnya) }}</td>
                            <td>{{ number_format($row->counter_saat_ganti) }}</td>
                            <td class="{{ $class }}">{{ number_format($row->selisih) }} Lbr</td>
                            <td class="{{ $class }}">{{ $status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                $lastRow = $rows->last();
                $pemakaianSekarang = max(0, $counterTerakhir - $lastRow->counter_saat_ganti);
                $classNow =
                    $pemakaianSekarang >= 80000
                        ? 'badge-danger'
                        : ($pemakaianSekarang >= 50000
                            ? 'badge-warning'
                            : 'badge-normal');
            @endphp
            <div class="counter-box">
                Pemakaian sejak ganti terakhir (counter {{ number_format($lastRow->counter_saat_ganti) }}
                &rarr; {{ number_format($counterTerakhir) }}):
                <span class="{{ $classNow }}">{{ number_format($pemakaianSekarang) }} Lbr</span>
            </div>
        </div>
    @endforeach

    <div class="footer">DGG System &copy; {{ date('Y') }} &mdash; Admin DGG Cirebon</div>
</body>

</html>
