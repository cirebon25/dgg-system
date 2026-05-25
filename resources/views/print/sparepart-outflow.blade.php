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
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }

        /* HEADER JUDUL TENGAH */
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
            background: #000;
            color: #fff;
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

        /* HEADER KUNING RATA TENGAH */
        .report-table th {
            background: #FFEB3B;
            border: 1px solid #000;
            padding: 8px 4px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
        }

        .report-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        /* KOLOM RATA TENGAH */
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
            width: 220px;
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

        .report-table th,
        .report-table td {
            word-wrap: break-word;
            /* Agar teks panjang tidak merusak kolom */
            overflow: hidden;
        }
    </style>
</head>

<body>

    <div class="doc-title">
        <h2>Rekap Pemakaian Sparepart</h2>
        {{-- REVISI: Menggunakan format Nama Bulan dan Tahun yang akurat --}}
        <p>Periode Bulan: {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM Y') }}</p>
    </div>

    @forelse($groupedUsages as $rayonName => $logs)
        <div class="rayon-badge-title"> RAYON: {{ strtoupper($rayonName) }}</div>

        <table class="report-table">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2">NO</th>
                    <th class="text-center" rowspan="2">TGL</th>
                    <th rowspan="2">NAMA CUSTOMER</th>
                    <th class="text-center" rowspan="2">TIPE</th>
                    <th class="text-center" rowspan="2">NO SERI</th>
                    <th rowspan="2">NAMA PART</th>
                    <th class="text-center" colspan="2">PEMAKAIAN BLN LALU</th>
                    <th class="text-center" colspan="2">COUNTER AKHIR</th>
                    <th class="text-center" rowspan="2">TEKNISI</th>
                </tr>
                <tr>
                    <th class="text-center">BW</th>
                    <th class="text-center">CL</th>
                    <th class="text-center">BW</th>
                    <th class="text-center">CL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $item->nama_customer }}</td>
                        <td class="text-center">{{ $item->tipe_model }}</td>
                        <td class="text-center font-mono">{{ $item->serial_number }}</td>
                        <td>{{ $item->nama_part }}</td>

                        <td class="text-center">{{ number_format($item->usage_bw ?? 0) }}</td>
                        <td class="text-center">{{ number_format($item->usage_color ?? 0) }}</td>

                        <td class="text-center font-mono">{{ number_format($item->counter_bw ?? 0) }}</td>
                        <td class="text-center font-mono">{{ number_format($item->counter_color ?? 0) }}</td>

                        <td class="text-center">{{ $item->nama_technician }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div style="text-align: center; padding: 20px; font-weight: bold;">❌ Tidak ada rekaman data.</div>
    @endforelse

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">CIREBON,
                {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y')) }}<br>KEPALA LOGISTIK GUDANG
            </div>
            <div class="line">( STAF ADMINISTRASI )</div>
        </div>
    </div>

    <script>
        window.onload = () => setTimeout(() => window.print(), 500);
    </script>
</body>

</html>
