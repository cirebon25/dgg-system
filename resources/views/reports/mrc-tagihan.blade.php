<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tagihan MRC - {{ $bulan ?? '' }} {{ $tahun ?? '' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        :root {
            --primary: #1d4ed8;
            --primary-dark: #1e3a8a;
            --primary-soft: #eff6ff;
            --bw: #334155;
            --bw-soft: #f1f5f9;
            --color: #0891b2;
            --color-soft: #ecfeff;
            --border: #e2e8f0;
            --text: #1e293b;
            --muted: #64748b;
            --warn-soft: #fef3c7;
            --warn-text: #92400e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 10px;
            color: var(--text);
            background: #f1f5f9;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 100%;
            background: #ffffff;
            border-radius: 14px;
            padding: 22px 24px 26px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08), 0 2px 6px rgba(15, 23, 42, .04);
        }

        /* ===== Header ===== */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding: 16px 20px;
            border-radius: 12px;
            background: linear-gradient(120deg, var(--primary-dark) 0%, var(--primary) 60%, #3b82f6 100%);
            color: #ffffff;
        }

        .title h2 {
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #ffffff;
        }

        .title p {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 12px;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .3px;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 999px;
        }

        .info {
            text-align: right;
            font-size: 10px;
            line-height: 1.6;
            color: #dbeafe;
        }

        /* ===== Table ===== */
        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--primary);
            color: #ffffff;
            padding: 8px 4px;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            text-align: center;
            vertical-align: middle;
            border: 1px solid rgba(255, 255, 255, .22);
        }

        /* Grup kolom BW & Color dibedakan warna */
        thead tr:first-child th:nth-child(6) {
            background: var(--bw);
        }

        thead tr:first-child th:nth-child(7) {
            background: var(--color);
        }

        thead tr:nth-child(2) th:nth-child(-n+7) {
            background: #475569;
        }

        thead tr:nth-child(2) th:nth-child(n+8) {
            background: #0e7490;
        }

        td {
            padding: 6px 5px;
            font-size: 10px;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        tbody tr:hover td {
            background: var(--primary-soft);
        }

        /* Customer dengan kelebihan pemakaian */
        tbody tr td.highlight-customer {
            background: var(--warn-soft) !important;
            color: var(--warn-text);
            font-weight: 700;
            border-left: 3px solid #f59e0b;
        }

        .belum-tercatat {
            color: #94a3b8;
            font-style: italic;
            background: #f1f5f9 !important;
        }

        .baseline-info {
            display: inline-block;
            margin-top: 2px;
            padding: 1px 6px;
            font-size: 8px;
            font-style: normal;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-soft);
            border-radius: 999px;
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

        /* Kolom total di sisi kanan */
        tbody td:nth-last-child(-n+3) {
            font-variant-numeric: tabular-nums;
        }

        tbody td:last-child {
            color: var(--primary-dark);
            background-color: var(--primary-soft) !important;
        }

        /* ===== Grand Total ===== */
        tfoot tr.grand-total td {
            padding: 12px 8px;
            font-size: 11px;
            color: #ffffff;
            background: linear-gradient(120deg, var(--primary-dark), var(--primary));
            border: none;
            letter-spacing: .3px;
        }

        tfoot tr.grand-total td:first-child {
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding-right: 16px;
        }

        /* ===== Ringkasan ===== */
        .summary-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 16px;
        }

        .summary-card {
            min-width: 0;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            background: #ffffff;
        }

        .summary-card .label {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .summary-card .value {
            margin-top: 3px;
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
        }

        .summary-card.accent {
            border-left-color: #16a34a;
            background: #f0fdf4;
        }

        .summary-card.accent .value {
            color: #15803d;
        }

        /* ===== Footer / Tanda tangan ===== */
        .footer-section {
            margin-top: 28px;
            display: flex;
            justify-content: flex-end;
            gap: 60px;
        }

        .signature {
            text-align: center;
            width: 160px;
            font-size: 10px;
            color: var(--muted);
        }

        .sig-line {
            margin-top: 54px;
            border-top: 1px solid var(--text);
        }

        /* ===== Tombol print ===== */
        .no-print {
            max-width: 100%;
            margin-bottom: 16px;
            text-align: center;
        }

        .no-print button {
            padding: 9px 24px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
            background: var(--primary);
            border: none;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(29, 78, 216, .35);
        }

        .no-print button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            * {
                box-shadow: none !important;
                text-shadow: none !important;
                transition: none !important;
            }

            .header-section,
            tfoot tr.grand-total td {
                background: var(--primary) !important;
            }

            .header-section,
            .table-wrap,
            .summary-card {
                border-radius: 0 !important;
            }

            .table-wrap {
                overflow: visible !important;
            }

            tbody tr:hover td {
                background: inherit;
            }

            tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Laporan</button>
        <input type="text" id="cari" placeholder="Cari customer / SN / model..."
            style="padding:8px 14px; width:280px; border:1px solid #cbd5e1; border-radius:999px; margin-left:8px;">
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

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Customer</th>
                        <th rowspan="2">SN Mesin</th>
                        <th rowspan="2">Model</th>
                        <th rowspan="2">Harga Sewa</th>
                        <th colspan="7">Black & White (BW)</th>
                        <th colspan="7">Color (CL)</th>
                        <th rowspan="2">Total Tagihan</th>
                        <th rowspan="2">PPN (11%)</th>
                        <th rowspan="2">Total + PPN</th>
                    </tr>
                    <tr>
                        {{-- Sub Header BW --}}
                        <th>Ctr Bln Lalu</th>
                        <th>Ctr Akhir</th>
                        <th>Usage</th>
                        <th>Free</th>
                        <th>Cash/Lbr</th>
                        <th>Lebih</th>
                        <th>Biaya</th>
                        {{-- Sub Header Color --}}
                        <th>Ctr Lalu</th>
                        <th>Ctr Akhir</th>
                        <th>Usage</th>
                        <th>Free</th>
                        <th>Cash/Lbr</th>
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

                            // Ambil harga per lembar dari relasi contract (sesuai hasil Tinker: harga_bw & harga_color)
                            $cashPerLbrBw = $item['contract']->harga_bw ?? 0;
                            $cashPerLbrColor = $item['contract']->harga_color ?? 0;
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
                                <td class="text-right">{{ $cashPerLbrBw > 0 ? number_format($cashPerLbrBw) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $t['kelebihan_bw'] > 0 ? number_format($t['kelebihan_bw']) : '-' }}</td>
                                <td class="text-right">{{ $t['biaya_bw'] > 0 ? number_format($t['biaya_bw']) : '-' }}
                                </td>
                            @else
                                <td class="text-center belum-tercatat" colspan="7">Belum dicatat MRC bulan ini</td>
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
                                <td class="text-right">
                                    {{ $cashPerLbrColor > 0 ? number_format($cashPerLbrColor) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $t['kelebihan_color'] > 0 ? number_format($t['kelebihan_color']) : '-' }}</td>
                                <td class="text-right">
                                    {{ $t['biaya_color'] > 0 ? number_format($t['biaya_color']) : '-' }}
                                </td>
                            @else
                                <td class="text-center belum-tercatat" colspan="7">-</td>
                            @endif

                            <td class="text-right">Rp {{ number_format($subTotal) }}</td>
                            <td class="text-right">Rp {{ number_format($ppn) }}</td>
                            <td class="text-right font-bold">Rp {{ number_format($totalWithPpn) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="22" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="grand-total">
                        <td colspan="19" class="text-right font-bold">GRAND TOTAL</td>
                        <td class="text-right font-bold">Rp {{ number_format($grandTotal) }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($grandPpn) }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($grandTotalPpn) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Ringkasan (hanya menampilkan variabel yang sudah ada, tanpa hitungan baru) --}}
        <div class="summary-section">
            <div class="summary-card">
                <div class="label">Total Tagihan</div>
                <div class="value">Rp {{ number_format($grandTotal) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">PPN (11%)</div>
                <div class="value">Rp {{ number_format($grandPpn) }}</div>
            </div>
            <div class="summary-card accent">
                <div class="label">Total + PPN</div>
                <div class="value">Rp {{ number_format($grandTotalPpn) }}</div>
            </div>
        </div>

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

    <script>
        document.getElementById('cari').addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('table tbody tr').forEach(function(tr) {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>

</body>

</html>
