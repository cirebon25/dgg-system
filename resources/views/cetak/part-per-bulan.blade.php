<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riwayat Ganti Part - {{ $namaBulan[$bulan] }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #111;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            font-size: 10px;
            color: #444;
            margin-top: 3px;
        }

        .mesin-section {
            margin-bottom: 16px;
        }

        .mesin-title {
            background: #1e3a5f;
            color: #fff;
            padding: 5px 10px;
            font-weight: bold;
            border-radius: 3px 3px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background: #374151;
            color: #fff;
            padding: 4px 7px;
            text-align: left;
        }

        td {
            padding: 4px 7px;
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

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>RIWAYAT GANTI SPAREPART BULAN {{ strtoupper($namaBulan[$bulan]) }} {{ $tahun }}</h2>
        <p>DGG System &mdash; Dicetak {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if ($data->isEmpty())
        <div class="empty">Tidak ada data penggantian part pada bulan ini.</div>
    @else
        @foreach ($data as $machineId => $rows)
            @php $machine = $rows->first()->machine; @endphp
            <div class="mesin-section">
                <div class="mesin-title">
                    Mesin: {{ $machine->serial_number }} &mdash; {{ $machine->tipe_model ?? '-' }} &mdash;
                    {{ $machine->customer?->nama_customer ?? 'Gudang DGG' }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sparepart</th>
                            <th>Tanggal</th>
                            <th>Counter Sebelumnya</th>
                            <th>Counter Saat Ganti</th>
                            <th>Selisih</th>
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
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->sparepart?->nama_sparepart ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ number_format($row->counter_sebelumnya) }}</td>
                                <td>{{ number_format($row->counter_saat_ganti) }}</td>
                                <td class="{{ $class }}">{{ number_format($row->selisih) }} Lbr</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <div class="footer">DGG System &copy; {{ date('Y') }} &mdash; Developer RUDIANTO</div>
</body>

</html>
