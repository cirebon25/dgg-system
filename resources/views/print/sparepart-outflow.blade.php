<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pemakaian Sparepart — Periode {{ $month }}/{{ $year }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @page { size: A4 landscape; margin: 10mm; }

        body {
            font-family: 'Source Sans 3', sans-serif;
            font-size: 10px;
            color: #111;
            background: #fff;
        }

        /* ── KOP ── */
        .kop {
            text-align: center;
            border-bottom: 2.5px double #111;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop h1 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .kop p  { font-size: 10px; margin-top: 2px; color: #444; }

        /* ── RAYON BADGE ── */
        .rayon-badge {
            display: inline-block;
            background-color: #ffea31;
            border: 1.5px solid #111;
            border-left: 4px solid #cf0707;
            color: #070000;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 12px;
            margin: 12px 0 5px 0;
        }

        /* ── TABLE ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            table-layout: fixed;
        }

        /* Header: pakai border bawah tebal + bold — tidak butuh background */
        .report-table thead tr.h1 th,
        .report-table thead tr.h2 th {
            background: #ffea31;
            color: #111;
            border: 2px solid #555;
            border-bottom: 1px solid #111;
            padding: 5px 4px;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            white-space: normal;
            line-height: 1.3;
        }

        /* Baris pertama header — garis atas tebal */
        .report-table thead tr.h1 th {
            border-top: 2px solid #111;
        }

        .report-table tbody tr { border-bottom: 1px solid #ccc; }
        .report-table tbody tr:nth-child(even) { background: #f5f5f3; }
        .report-table td {
            border: 1px solid #ccc;
            padding: 4px 5px;
            vertical-align: middle;
            font-size: 9.5px;
            word-wrap: break-word;
        }

        .tc   { text-align: center; }
        .mono { font-family: 'Courier New', monospace; font-size: 9px; font-weight: 600; }

        /* ── NAMA PART + JUMLAH ── */
        /* Format: Toner Canon M-643 (1) — tanpa background */
        .part-list { line-height: 1.9; }
        .part-row  { display: block; font-size: 9.5px; }
        .part-name { font-weight: 600; }
        .part-qty  {
            font-weight: 700;
            font-size: 9px;
            color: #444;
            margin-left: 2px;
        }

        /* ── TANDA TANGAN ── */
        .signature-area { display: flex; justify-content: flex-end; margin-top: 24px; }
        .signature-box  { text-align: center; width: 200px; }
        .signature-box .lbl  { font-size: 10px; font-weight: 700; margin-bottom: 44px; text-transform: uppercase; }
        .signature-box .line { border-top: 1px solid #111; font-size: 10px; font-weight: 700; padding-top: 4px; }

        /* ── PRINT ── */
        @media print {
            body { background: #fff; }
            /* Even rows tetap tampil saat print */
            .report-table tbody tr:nth-child(even) {
                background: #f5f5f3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    {{-- KOP --}}
    <div class="kop">
        <h1>Rekap Pemakaian Sparepart</h1>
        <p>Periode Bulan: {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->isoFormat('MMMM Y') }}</p>
    </div>

    @forelse ($groupedUsages as $rayonName => $items)

        <div class="rayon-badge">Rayon: {{ strtoupper($rayonName) }}</div>

        @php
            $visits = collect($items)->groupBy('_visit_key');
        @endphp

        <table class="report-table">
            <colgroup>
                <col style="width:28px;">
                <col style="width:62px;">
                <col>
                <col style="width:65px;">
                <col style="width:100px;">
                <col>
                <col style="width:38px;">
                <col style="width:38px;">
                <col style="width:55px;">
                <col style="width:55px;">
                <col style="width:70px;">
            </colgroup>
            <thead>
                <tr class="h1">
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
                <tr class="h2">
                    <th>BW</th>
                    <th>CL</th>
                    <th>BW</th>
                    <th>CL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visits as $visitKey => $visitItems)
                    @php
                        $first = $visitItems->first();

                        $parts = $visitItems
                            ->groupBy('nama_part')
                            ->map(fn($g) => $g->sum('jumlah_part'))
                            ->map(fn($qty, $nama) => ['nama' => $nama, 'qty' => $qty]);
                    @endphp
                    <tr>
                        <td class="tc">{{ $loop->iteration }}</td>
                        <td class="tc">
                            {{ $first->tanggal ? \Carbon\Carbon::parse($first->tanggal)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $first->nama_customer }}</td>
                        <td class="tc">{{ $first->tipe_model }}</td>
                        <td class="tc mono">{{ $first->serial_number }}</td>
                        <td>
                            <div class="part-list">
                                @foreach ($parts as $part)
                                    {{-- Nama Part (1) — tanpa background --}}
                                    <span class="part-row">
                                        <span class="part-name">{{ $part['nama'] }}</span><span class="part-qty"> ({{ $part['qty'] }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="tc">{{ number_format($first->usage_bw) }}</td>
                        <td class="tc">{{ number_format($first->usage_color) }}</td>
                        <td class="tc mono">{{ number_format($first->counter_bw) }}</td>
                        <td class="tc mono">{{ number_format($first->counter_color) }}</td>
                        <td class="tc">{{ $first->nama_technician }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="tc" style="padding:16px; color:#999;">
                            Tidak ada data untuk rayon ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @empty
        <div style="text-align:center; padding:30px; color:#999; font-size:12px;">
            Tidak ada data pemakaian sparepart pada periode ini.
        </div>
    @endforelse

    <div class="signature-area">
        <div class="signature-box">
            <div class="lbl">
                Cirebon, {{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y')) }}
            </div>
            <div class="line">( Admin Gudang )</div>
        </div>
    </div>

    <script>
        window.onload = () => setTimeout(() => window.print(), 500);
    </script>

</body>
</html>