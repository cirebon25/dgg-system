<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Stok — {{ $sparepart->nama_sparepart }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        .info-box {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            background: #f8fafc;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            display: block;
        }

        .info-box span {
            font-weight: bold;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e3a5f;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-size: 11px;
        }

        th.right,
        td.right {
            text-align: right;
        }

        th.center,
        td.center {
            text-align: center;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .masuk {
            color: #15803d;
            font-weight: bold;
        }

        .keluar {
            color: #dc2626;
            font-weight: bold;
        }

        .retur {
            color: #2563eb;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9.5px;
            font-weight: bold;
        }

        .badge-masuk {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-keluar {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-retur {
            background: #dbeafe;
            color: #2563eb;
        }

        .saldo-positif {
            color: #15803d;
            font-weight: bold;
        }

        .saldo-nol {
            color: #dc2626;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background: #e8f0fe;
            padding: 8px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                size: A4 landscape;
                margin: 12mm;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Kartu Stok Sparepart</h2>
        <p>{{ strtoupper($sparepart->nama_sparepart) }}
            {{ $sparepart->code_part ? '— Kode: ' . $sparepart->code_part : '' }}</p>
        <p>Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
    </div>

    <div class="info-box">
        <div>
            <label>Nama Sparepart</label>
            <span>{{ $sparepart->nama_sparepart }}</span>
        </div>
        <div>
            <label>Kode Part</label>
            <span>{{ $sparepart->code_part ?? '-' }}</span>
        </div>
        <div>
            <label>Stok Gudang Saat Ini</label>
            <span class="{{ $sparepart->stok > 0 ? 'saldo-positif' : 'saldo-nol' }}">
                {{ $sparepart->stok }} pcs
            </span>
        </div>
        <div>
            <label>Total Transaksi</label>
            <span>{{ $transaksi->count() }} transaksi</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="center" style="width:30px;">No</th>
                <th style="width:130px;">Tanggal</th>
                <th>Keterangan</th>
                <th class="center" style="width:70px;">Tipe</th>
                <th class="right" style="width:70px;">Masuk</th>
                <th class="right" style="width:70px;">Keluar</th>
                <th class="right" style="width:80px;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $i => $t)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($t['tanggal'])->isoFormat('D MMM YYYY, HH:mm') }}</td>
                    <td>{{ $t['keterangan'] }}</td>
                    <td class="center">
                        @php
                            $badgeClass = match ($t['tipe']) {
                                'MASUK' => 'badge-masuk',
                                'KELUAR' => 'badge-keluar',
                                'RETUR' => 'badge-retur',
                                default => '',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $t['tipe'] }}</span>
                    </td>
                    <td class="right masuk">{{ $t['masuk'] > 0 ? '+ ' . number_format($t['masuk']) : '-' }}</td>
                    <td class="right keluar">{{ $t['keluar'] > 0 ? '- ' . number_format($t['keluar']) : '-' }}</td>
                    <td class="right {{ $t['saldo'] > 0 ? 'saldo-positif' : 'saldo-nol' }}">
                        {{ number_format($t['saldo']) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">
                        Belum ada transaksi untuk sparepart ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($transaksi->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="4">Total</td>
                    <td class="right masuk">+ {{ number_format($transaksi->sum('masuk')) }}</td>
                    <td class="right keluar">- {{ number_format($transaksi->sum('keluar')) }}</td>
                    <td class="right">{{ number_format($transaksi->last()['saldo'] ?? 0) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">DGG System © {{ date('Y') }} — PT Dinamika Global Gemilang</div>

    <script>
        window.onload = () => window.print();
    </script>
</body>

</html>
