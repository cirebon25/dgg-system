<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Pemakaian Sparepart — Periode {{ $month }}/{{ $year }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .doc-title h1 {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 0;
        }

        .doc-title h2 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .doc-title p {
            font-size: 12px;
            font-weight: 600;
            margin: 0;
        }

        .rayon-badge-title {
            background: #6b6a6a;
            color: #eee4e4;
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 15px 0 5px 0;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .report-table th {
            background: #FFEB3B;
            border: 1px solid #000;
            padding: 4px 2px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            overflow: hidden;
            white-space: normal;
        }

        .report-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center !important;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .signature-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-box .lbl {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .signature-box .line {
            border-top: 1px solid #000;
            font-size: 11px;
            font-weight: 700;
            padding-top: 4px;
        }
    </style>
</head>

<body>

    <div class="doc-title">
        <h2>Rekap Pemakaian Sparepart</h2>
        <p>Periode Bulan: {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM Y') }}</p>
    </div>

    @forelse($groupedUsages as $rayonName => $logs)
        <div class="rayon-badge-title"> RAYON: {{ strtoupper($rayonName) }}</div>

        @php
            // Kelompokkan berdasarkan kunjungan (tanggal + serial_number + teknisi)
            // Sehingga 1 kunjungan = 1 baris, meski pakai banyak sparepart
            $grouped = collect($logs)->groupBy(function ($item) {
                return $item->tanggal . '|' . $item->serial_number . '|' . $item->nama_technician;
            });
        @endphp

        <table class="report-table">
            <colgroup>
                <col style="width: 30px;">
                <col style="width: 80px;">
                <col>
                <col style="width: 70px;">
                <col style="width: 120px;">
                <col>
                <col style="width: 40px;">
                <col style="width: 40px;">
                <col style="width: 60px;">
                <col style="width: 60px;">
                <col style="width: 80px;">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">TGL</th>
                    <th rowspan="2">NAMA CUSTOMER</th>
                    <th rowspan="2">TIPE</th>
                    <th rowspan="2">NO SERI</th>
                    <th rowspan="2">NAMA PART</th>
                    <th colspan="2">PEMAKAIAN BLN LALU</th>
                    <th colspan="2">COUNTER AKHIR</th>
                    <th rowspan="2">TEKNISI</th>
                </tr>
                <tr>
                    <th>BW</th>
                    <th>CL</th>
                    <th>BW</th>
                    <th>CL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grouped as $index => $items)
                    @php
                        // Ambil data kunjungan dari baris pertama
                        $first = $items->first();

                        // Gabungkan semua nama part jadi satu string dipisah koma
                        $namaParts = $items->pluck('nama_part')->filter()->unique()->implode(', ');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($first->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $first->nama_customer }}</td>
                        <td class="text-center">{{ $first->tipe_model }}</td>
                        <td class="text-center font-mono">{{ $first->serial_number }}</td>
                        <td>{{ $namaParts ?: '-' }}</td>
                        <td class="text-center">{{ number_format($first->usage_bw ?? 0) }}</td>
                        <td class="text-center">{{ number_format($first->usage_color ?? 0) }}</td>
                        <td class="text-center font-mono">{{ number_format($first->counter_bw ?? 0) }}</td>
                        <td class="text-center font-mono">{{ number_format($first->counter_color ?? 0) }}</td>
                        <td class="text-center">{{ $first->nama_technician }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div style="text-align: center; padding: 20px; font-weight: bold;">❌ Tidak ada rekaman data.</div>
    @endforelse

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">CIREBON, {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y')) }}
            </div>
            <div class="line">( ADMIN GUDANG )</div>
        </div>
    </div>

    <script>
        window.onload = () => setTimeout(() => window.print(), 500);
    </script>
</body>

</html>
