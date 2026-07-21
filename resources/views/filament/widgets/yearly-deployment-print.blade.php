<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Laporan Tahunan ({{ $startYear }} - {{ $endYear }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN TREN PENEMPATAN MESIN TAHUNAN</h2>
        <p>Periode: Tahun {{ $startYear }} s/d {{ $endYear }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tahun Pemasangan</th>
                <th>Total Pemasangan Baru (Unit)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $year => $count)
                <tr>
                    <td>{{ $year }}</td>
                    <td>{{ $count }} Unit</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
