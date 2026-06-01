{{-- resources/views/print/withdrawal-rekap.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Penarikan {{ $namaBulan }} {{ $year }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
        }

        header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        header h1 {
            margin: 0;
            font-size: 15px;
        }

        header h2 {
            margin: 4px 0 0 0;
            font-size: 12px;
            font-weight: normal;
        }

        header p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background: #1e3a8a;
            color: #fff;
            padding: 8px 6px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        td {
            padding: 7px 6px;
            border: 1px solid #aaa;
            vertical-align: middle;
            font-size: 10px;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }

        .badge-baik {
            background: #dcfce7;
            color: #166534;
        }

        .badge-ringan {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-berat {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .ttd {
            text-align: center;
            width: 200px;
        }

        .summary {
            margin-top: 12px;
            font-size: 10px;
            background: #f1f5f9;
            padding: 8px 12px;
            border-radius: 4px;
            display: flex;
            gap: 24px;
        }

        .summary span {
            font-weight: bold;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>PT. DINAMIKA GLOBAL GEMILANG</h1>
        <h2>REKAP PENARIKAN UNIT MESIN — {{ strtoupper($namaBulan) }} {{ $year }}</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </header>

    <div class="summary">
        Total Penarikan: <span>{{ $records->count() }} Unit</span>
        &nbsp;|&nbsp; Kondisi Baik: <span>{{ $records->where('kondisi_akhir', 'Baik / Ready Gudang')->count() }}</span>
        &nbsp;|&nbsp; Rusak Ringan: <span>{{ $records->where('kondisi_akhir', 'Rusak Ringan')->count() }}</span>
        &nbsp;|&nbsp; Rusak Berat: <span>{{ $records->where('kondisi_akhir', 'Rusak Berat')->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Tgl Tarik</th>
                <th width="14%">SN Mesin</th>
                <th width="12%">Tipe Model</th>
                <th width="20%">Customer</th>
                <th width="13%">Kondisi Akhir</th>
                <th>Alasan Penarikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_tarik)->format('d/m/Y') }}</td>
                    <td class="text-center"><b>{{ $row->machine->serial_number ?? '-' }}</b></td>
                    <td class="text-center">{{ $row->machine->tipe_model ?? '-' }}</td>
                    <td>{{ $row->customer->nama_customer ?? '-' }}</td>
                    <td class="text-center">
                        {{-- @if ($row->kondisi_akhir === 'Baik')
                            <span class="badge badge-baik">Baik / Ready</span>
                        @elseif($row->kondisi_akhir === 'Rusak Ringan')
                            <span class="badge badge-ringan">Rusak Ringan</span>
                        @else
                            <span class="badge badge-berat">Rusak berat</span>
                        @endif --}}
                    </td>
                    <td>{{ $row->alasan_penarikan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding:20px; color:#999;">
                        Tidak ada data penarikan pada periode {{ $namaBulan }} {{ $year }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div></div>
        <div class="ttd">
            <p>Indramayu, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <br><br><br>
            <strong>( _________________________ )</strong>
            <p>Admin Operasional</p>
        </div>
    </div>
</body>

</html>
