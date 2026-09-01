Berikut adalah kode HTML/Blade yang telah diperbarui. Latar belakang biru (`background-color: #0509f34d`) kini **hanya
diterapkan pada kolom Nama Customer** saja ketika baris tersebut memenuhi kondisi `hasExcess` dan `tercatat`, sementara
kolom lainnya tetap putih/normal:

```html
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tagihan MRC - {{ $bulan ?? '' }} {{ $tahun ?? '' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 10px;
            color: #334155;
        }

        .container {
            width: 100%;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .title h2 {
            color: #2563eb;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background: #2563eb;
            color: #ffffff;
            padding: 6px 3px;
            font-size: 9px;
            text-transform: uppercase;
            text-align: center;
            border: 1px solid #1d4ed8;
        }

        td {
            padding: 5px 3px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .highlight-customer {
            background-color: #0509f34d !important;
        }

        .belum-tercatat {
            color: #94a3b8;
            font-style: italic;
        }

        .baseline-info {
            color: #2564eb50;
            font-style: italic;
            font-size: 9px;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Footer */
        .footer-section {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 60px;
        }

        .signature {
            text-align: center;
            width: 150px;
        }

        .sig-line {
            margin-top: 50px;
            border-top: 1px solid #000;
        }

        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8fafc;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding:8px 20px; cursor:pointer;">🖨️ Print Laporan</button>
    </div>

    <div class="container">
        <div class="header-section">
            <div class="title">
                <h2>Laporan Tagihan MRC</h2>
                <p>Periode: {{ $bulan ?? '' }} {{ $tahun ?? '' }}</p>
            </div>
            <div class="info">
                Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Customer</th>
                    <th rowspan="2">SN Mesin</th>
                    <th rowspan="2">Model</th>
                    <th rowspan="2">Harga Sewa</th>
                    <th colspan="6">Black & White (BW)</th>
                    <th colspan="6">Color (CL)</th>
                    <th rowspan="2">Total Tagihan</th>
                    <th rowspan="2">PPN (11%)</th>
                    <th rowspan="2">Total + PPN</th>
                </tr>
                <tr>
                    <th>Ctr Lalu</th>
                    <th>Ctr Akhir</th>
                    <th>Usage</th>
                    <th>Free</th>
                    <th>Lebih</th>
                    <th>Biaya</th>
                    <th>Ctr Lalu</th>
                    <th>Ctr Akhir</th>
                    <th>Usage</th>
                    <th>Free</th>
                    <th>Lebih</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $grandPpn = 0;
                    $grandTotalPpn = 0;
                @endphp
                @forelse ($tagihans ?? [] as $i => $item)
                    @php
                        $t = $item['tagihan'];
                        $subTotal = $t['total'];

                        $ppn = $subTotal * 0.11;
                        $totalWithPpn = $subTotal + $ppn;

                        $grandTotal += $subTotal;
                        $grandPpn += $ppn;
                        $grandTotalPpn += $totalWithPpn;

                        $hasExcess = $t['kelebihan_bw'] > 0 || $t['kelebihan_color'] > 0;
                        $tercatat = $item['is_mrc_tercatat'] ?? false;
                        $isBaseline = $item['is_baseline_pertama'] ?? false;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-left {{ $hasExcess && $tercatat ? 'highlight-customer' : '' }}">
                            {{ $item['contract']->customer?->nama_customer ?? '-' }}
                        </td>
                        <td class="text-center">{{ $item['contract']->machine?->serial_number ?? '-' }}</td>
                        <td class="text-center">{{ $item['contract']->machine?->tipe_model ?? '-' }}</td>
                        <td class="text-right">{{ number_format($t['harga_sewa']) }}</td>

                        {{-- Black & White --}}
                        @if ($tercatat)
                            <td class="text-center">{{ number_format($item['counter_bw_lalu']) }}</td>
                            <td class="text-center">{{ number_format($item['counter_bw_akhir']) }}</td>
                            <td class="text-center">
                                {{ number_format($t['usage_bw']) }}
                                @if ($isBaseline)
                                    <br><span class="baseline-info">(Baseline)</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($t['free_bw']) }}</td>
                            <td class="text-center">
                                {{ $t['kelebihan_bw'] > 0 ? number_format($t['kelebihan_bw']) : '-' }}</td>
                            <td class="text-right">{{ $t['biaya_bw'] > 0 ? number_format($t['biaya_bw']) : '-' }}</td>
                        @else
                            <td class="text-center belum-tercatat" colspan="6">Belum dicatat MRC bulan ini</td>
                        @endif

                        {{-- Color --}}
                        @if ($tercatat)
                            <td class="text-center">{{ number_format($item['counter_color_lalu']) }}</td>
                            <td class="text-center">{{ number_format($item['counter_color_akhir']) }}</td>
                            <td class="text-center">
                                {{ number_format($t['usage_color']) }}
                                @if ($isBaseline)
                                    <br><span class="baseline-info">(Baseline)</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($t['free_color']) }}</td>
                            <td class="text-center">
                                {{ $t['kelebihan_color'] > 0 ? number_format($t['kelebihan_color']) : '-' }}</td>
                            <td class="text-right">{{ $t['biaya_color'] > 0 ? number_format($t['biaya_color']) : '-' }}
                            </td>
                        @else
                            <td class="text-center belum-tercatat" colspan="6">-</td>
                        @endif

                        <td class="text-right">Rp {{ number_format($subTotal) }}</td>
                        <td class="text-right">Rp {{ number_format($ppn) }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($totalWithPpn) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #e2e8f0;">
                    <td colspan="17" class="text-right font-bold" style="padding:10px;">GRAND TOTAL</td>
                    <td class="text-right font-bold">Rp {{ number_format($grandTotal) }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($grandPpn) }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($grandTotalPpn) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-section">
            <div class="signature">
                <p>Kepala Cabang</p>
                <div class="sig-line"></div>
            </div>
            <div class="signature">
                <p>Dibuat oleh,</p>
                <div class="sig-line"></div>
            </div>
        </div>
    </div>

</body>

</html>

```
