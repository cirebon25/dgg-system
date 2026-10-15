<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Order Part {{ $order->no_order }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .kop h1 {
            margin: 0;
            font-size: 16px;
        }

        .kop p {
            margin: 2px 0 0;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            font-size: 14px;
            margin: 0 0 12px;
            text-decoration: underline;
        }

        .info {
            width: 100%;
            margin-bottom: 12px;
        }

        .info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info td:first-child {
            width: 90px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 5px 6px;
        }

        table.items th {
            background: #eee;
            text-align: center;
        }

        .c {
            text-align: center;
        }

        .sign {
            width: 100%;
            margin-top: 40px;
        }

        .sign td {
            width: 50%;
            text-align: center;
            height: 90px;
            vertical-align: top;
        }

        .no-print {
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
    </div>

    <div class="kop">
        <h1>PT. DINAMIKA GLOBAL GEMILANG</h1>
        <p>Cirebon</p>
    </div>

    <h2>ORDER PART</h2>

    <table class="info">
        <tr>
            <td>No Order</td>
            <td>: {{ $order->no_order }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ ucfirst($order->status) }}</td>
        </tr>
        @if ($order->catatan)
            <tr>
                <td>Catatan</td>
                <td>: {{ $order->catatan }}</td>
            </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50px">No</th>
                <th style="width:150px">Kode Part</th>
                <th style="width:90px">No Part</th>
                <th>Nama Part</th>
                <th style="width:50px">Stok</th>
                <th style="width:80px">Jml Order</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $i => $item)
                <tr>
                    @php $sp = $spareparts[$item->sparepart_id] ?? null; @endphp
                    <td class="c">{{ $i + 1 }}</td>
                    <td class="c">{{ $sp->code_part ?? '-' }}</td>
                    <td class="c">{{ $sp->no_part ?? '-' }}</td>
                    <td>{{ $sp->nama_sparepart ?? '-' }}</td>
                    <td class="c">{{ $item->stok_saat_ini }}</td>
                    <td class="c">{{ $item->jumlah_order }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="c">Tidak ada item</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign">
        <tr>
            <td>Dibuat oleh,<br><br><br><br>(................................)</td>
            <td>Disetujui oleh,<br><br><br><br>(................................)</td>
        </tr>
    </table>
</body>

</html>
