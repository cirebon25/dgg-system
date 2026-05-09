<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ranking Pemakaian DGG</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #9c8989; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #8a8080; padding: 8px; text-align: center; }
        th { background: #d3bfbf; }
        .text-left { text-align: left; }
        .total { font-weight: bold; background: #ddd6b9; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <h3>RANKING PEMAKAIAN CUSTOMER PERIODE {{ strtoupper($bulan) }} {{ $tahun }}</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>RANK</th>
                <th class="text-left">NAMA CUSTOMER</th>
                <th>SN MESIN</th>
                <th>TOTAL BW</th>
                <th>TOTAL COLOR</th>
                <th>GRAND TOTAL</th>
                <th>TOTAL PEMAKAIAN</th>
                <th>LAMA PASANG</th>
                <th>RATA-RATA / BULAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left"><strong>{{ $row->nama_customer }}</strong></td>
                <td>{{ $row->serial_number }}</td>
                <td style="color: blue">{{ number_format($row->total_bw) }}</td>
                <td style="color: red">{{ number_format($row->total_color) }}</td>
                <td class="total">{{ number_format($row->total_semua) }}</td>
                <td>{{ number_format($row->total_semua) }}</td>
                <td>{{ $row->jumlah_bulan }} bln</td>
                <td style="background: #8998a1; font-weight: bold;">
                    {{ number_format($row->rata_rata) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>