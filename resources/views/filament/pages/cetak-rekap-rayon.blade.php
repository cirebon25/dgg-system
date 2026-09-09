<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional DGG - {{ $namaBulan }} {{ $year }}</title>
    <style>
        /* 1. RESET & PRINT SETTINGS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 9px;
            padding: 10px 15px;
            color: #000;
            line-height: 1.1;
            background: #fff;
        }

        /* 2. HEADER UTAMA */
        .header {
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 2px !important;
            margin-bottom: 5px !important;
        }

        .header h2 {
            font-size: 16px;
            text-transform: uppercase;
            line-height: 1.0;
        }

        .header p {
            font-size: 10px;
            font-weight: bold;
            line-height: 1.0;
        }

        /* TANGGAL CETAK PER HALAMAN */
        .print-date-page {
            text-align: right;
            font-size: 8px;
            font-style: italic;
            margin-bottom: 2px;
        }

        /* 3. RAYON & KOTA HEADER */
        .rayon-box {
            page-break-before: always;
        }

        .rayon-box:first-of-type {
            page-break-before: auto;
        }

        .rayon-header {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 10px !important;
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-transform: uppercase;
        }

        .kota-subheader {
            padding: 3px 10px;
            font-weight: bold;
            font-size: 9px;
            border-bottom: 1px dashed #000;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* 4. TABLE STYLING */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 5px !important;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 2px !important;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #fff !important;
            font-size: 8px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
        }

        /* 5. WARNA TEKS TIPE KUNJUNGAN (TANPA BACKGROUND) */
        .type-rm {
            color: #2563eb !important;
            font-weight: bold;
        }

        /* Biru */
        .type-rn {
            color: #16a34a !important;
            font-weight: bold;
        }

        /* Hijau */
        .type-cm {
            color: #dc2626 !important;
            font-weight: bold;
        }

        /* Merah */
        .type-default {
            color: #000 !important;
            font-weight: bold;
        }

        /* SUMMARY FOOTER BERSIH */
        .summary-footer {
            margin-top: 5px;
            padding: 8px 10px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .summary-footer b,
        .summary-footer span {
            font-size: 13px !important;
        }

        @media print {
            @page {
                size: landscape;
                margin: 0.5cm;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <p>LAPORAN PENGERJAAN UNIT & MAINTENANCE PER RAYON ({{ $namaBulan }} {{ $year }})</p>
    </div>

    @foreach ($rayons as $rayon)
        <div class="rayon-box">
            <div class="print-date-page">Lap. Service Tanggal Cetak: {{ date('d/m/Y H:i') }}</div>

            @php
                $rayonTotalUnit = 0;
                $rayonRM = 0;
                $rayonCM = 0;
                $rayonNotVisited = 0;
                $customersByCity = $rayon->customers->groupBy('kota');
            @endphp

            <div class="rayon-header">
                <span>📍 RAYON: {{ strtoupper($rayon->nama_rayon) }}</span>
                <span>👨‍🔧 TIM: {{ $rayon->technicians->pluck('nama_technician')->implode(', ') }}</span>
            </div>

            @foreach ($customersByCity as $kota => $customers)
                <div class="kota-subheader">🏙️ KOTA/KAB: {{ strtoupper($kota ?: 'Lainnya') }}</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 3%;">No</th>
                            <th style="width: 8%;">Tanggal</th>
                            <th style="width: 10%;">SN Mesin</th>
                            <th style="width: 18%;">Customer</th>
                            <th style="width: 4%;">Tipe</th>
                            <th style="width: 10%;">Counter</th>
                            <th style="width: 10%;">Usage</th>
                            <th style="width: 15%;">Sparepart</th>
                            <th style="width: 14%;">Perbaikan</th>
                            <th style="width: 8%;">Teknisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($customers as $customer)
                            @php
                                // Ambil semua deployment unit milik customer ini
                                $deployments = $customer->deployments;
                                $rayonTotalUnit += $deployments->count();
                            @endphp

                            @foreach ($deployments as $dep)
                                @php
                                    // Ambil log service pada bulan & tahun tersebut khusus untuk mesin ini
                                    $logs = \App\Models\ServiceLog::where('machine_id', $dep->machine_id)
                                        ->whereMonth('tanggal', $month)
                                        ->whereYear('tanggal', $year)
                                        ->with(['technician', 'serviceLogSpareparts.sparepart'])
                                        ->get();

                                    $rayonRM += $logs->where('tipe_kunjungan', 'RM')->count();
                                    $rayonCM += $logs->where('tipe_kunjungan', 'CM')->count();
                                @endphp

                                @if ($logs->isEmpty())
                                    @php $rayonNotVisited++; @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>-</td>
                                        <td>{{ $dep->machine->serial_number ?? '-' }}</td>
                                        <td style="text-align:left;"><strong>{{ $customer->nama_customer }}</strong>
                                        </td>
                                        <td colspan="5" style="font-style: italic; font-size: 10px; color: #dc2626;">
                                            BELUM DIKUNJUNGI
                                        </td>
                                        <td>-</td>
                                    </tr>
                                @else
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $log->tanggal ? $log->tanggal->format('d/m/y') : '-' }}</td>
                                            <td>{{ $log->machine->serial_number ?? '-' }}</td>
                                            <td style="text-align:left;">
                                                <strong>{{ $customer->nama_customer }}</strong></td>
                                            <td>
                                                <span
                                                    class="
                                                @if ($log->tipe_kunjungan == 'RM') type-rm
                                                @elseif($log->tipe_kunjungan == 'RN') type-rn
                                                @elseif($log->tipe_kunjungan == 'CM') type-cm
                                                @else type-default @endif">
                                                    {{ $log->tipe_kunjungan }}
                                                </span>
                                            </td>
                                            <td>
                                                <span>C: {{ number_format($log->counter_color ?? 0) }}</span><br>
                                                <span>B: {{ number_format($log->counter_bw ?? 0) }}</span>
                                            </td>
                                            <td>
                                                <span>C: {{ number_format($log->usage_color ?? 0) }}</span><br>
                                                <span>B: {{ number_format($log->usage_bw ?? 0) }}</span>
                                            </td>
                                            <td style="text-align:left;">
                                                @foreach ($log->serviceLogSpareparts as $sp)
                                                    • {{ $sp->sparepart->nama_sparepart ?? '' }}
                                                    <b>({{ $sp->jumlah }})</b><br>
                                                @endforeach
                                            </td>
                                            <td style="text-align:left;">{{ $log->perbaikan }}</td>
                                            <td>
                                                <b>{{ $log->technician->nama_technician ?? '-' }}</b>
                                                @if ($log->nama_teknisi_2)
                                                    <br><span style="font-size: 7px;">&
                                                        {{ $log->nama_teknisi_2 }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endforeach

            <div class="summary-footer">
                <b>RINGKASAN RAYON {{ strtoupper($rayon->nama_rayon) }}:</b>
                <span>POPULASI: <b>{{ $rayonTotalUnit }} UNIT</b></span>
                <span>TOTAL RM: <b>{{ $rayonRM }}</b></span>
                <span>TOTAL CM: <b>{{ $rayonCM }}</b></span>
                <span>BELUM DIKUNJUNGI: <b>{{ $rayonNotVisited }} UNIT</b></span>
            </div>
        </div>
    @endforeach
</body>

</html>
