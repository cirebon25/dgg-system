<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $dari->format('d/m/Y') }} - {{ $sampai->format('d/m/Y') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 24px;
            color: #000;
        }

        h2,
        h3 {
            margin: 0;
            text-align: center;
        }

        h3 {
            font-weight: normal;
            margin-bottom: 4px;
        }

        .periode {
            text-align: center;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        th {
            background: #eee;
        }

        td.c {
            text-align: center;
        }

        .noprint {
            margin-bottom: 12px;
        }

        @media print {
            .noprint {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="noprint">
        <button onclick="window.print()">Cetak</button>
    </div>

    <h2>PT Dinamika Global Gemilang</h2>
    <h3>Rekap Absensi Karyawan</h3>
    <div class="periode">
        Periode {{ $dari->translatedFormat('d F Y') }} s/d {{ $sampai->translatedFormat('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th style="width:100px">Tanggal</th>
                <th style="width:60px">PIN</th>
                <th>Nama</th>
                <th style="width:70px">Masuk</th>
                <th style="width:70px">Pulang</th>
                <th style="width:60px">Scan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td class="c">{{ \Illuminate\Support\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                    <td class="c">{{ $row['pin'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="c">{{ $row['masuk'] }}</td>
                    <td class="c">{{ $row['pulang'] }}</td>
                    <td class="c">{{ $row['jumlah'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="c">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
