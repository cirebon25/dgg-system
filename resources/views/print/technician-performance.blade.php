<!DOCTYPE html>
<html>

<head>
    <title>Laporan Kinerja Teknisi DGG</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f4;
            padding: 20px;
            color: #333;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h2 {
            border-bottom: 2px solid #005088;
            padding-bottom: 10px;
            color: #005088;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #005088;
            color: white;
        }

        .bar-container {
            background: #eee;
            border-radius: 5px;
            height: 15px;
            width: 100%;
            position: relative;
            margin-top: 5px;
        }

        .bar-fill {
            background: #11caa0;
            height: 100%;
            border-radius: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #d9534f; color: white; border: none; cursor: pointer;">Cetak
            Laporan</button>
    </div>

    <div class="card">
        <h2>Laporan Persentase Kinerja Teknisi</h2>
        <p>Periode: {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}</p>

        @foreach ($reportData as $data)
            <div style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 15px;">
                <h3 style="margin-bottom: 5px;">{{ $data->nama }} <span
                        style="font-weight: normal; font-size: 14px;">(Total: {{ $data->total }} Kunjungan)</span></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tipe Kunjungan</th>
                            <th>Jumlah</th>
                            <th>Persentase (%)</th>
                            <th style="width: 300px;">Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->details as $tipe => $info)
                            <tr>
                                <td><strong>{{ $tipe }}</strong></td>
                                <td>{{ $info['count'] }}</td>
                                <td>{{ $info['percentage'] }}%</td>
                                <td>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: {{ $info['percentage'] }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
