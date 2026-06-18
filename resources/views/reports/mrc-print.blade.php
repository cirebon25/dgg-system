<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Status MRC - {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 12px;
        }

        .summary {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .summary-box {
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .box-total {
            background: #e0e7ff;
            color: #3730a3;
        }

        .box-sudah {
            background: #d1fae5;
            color: #065f46;
        }

        .box-belum {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #333;
            padding: 5px 6px;
            background: #d0d0d0;
            font-weight: bold;
            text-align: left;
        }

        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
        }

        .status-sudah {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
        }

        .status-belum {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .ttd {
            text-align: center;
            width: 180px;
        }

        .ttd .line {
            margin-top: 55px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 210mm;
            margin: 10px auto;
            padding: 10px 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Rekap Status MRC — {{ $bulan }} {{ $tahun }}</h2>
            <p>Periode &nbsp;&nbsp;: {{ $bulan }} {{ $tahun }}</p>
            <p>Dicetak &nbsp;&nbsp;: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
        <hr>

        @php
            $totalMesin = $machines->count();
            $sudah = $machines->filter(fn($m) => isset($mrcLogs[$m->id]))->count();
            $belum = $totalMesin - $sudah;
        @endphp

        <div class="summary">
            <div class="summary-box box-total">Total Mesin: {{ $totalMesin }}</div>
            <div class="summary-box box-sudah">✅ Sudah MRC: {{ $sudah }}</div>
            <div class="summary-box box-belum">❌ Belum MRC: {{ $belum }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Customer</th>
                    <th style="width:95px;">SN Mesin</th>
                    <th style="width:80px;">Model</th>
                    <th style="width:75px;">Teknisi</th>
                    <th class="center" style="width:65px;">Status</th>
                    <th class="center" style="width:70px;">Ctr BW</th>
                    <th class="center" style="width:60px;">Usage BW</th>
                    <th class="center" style="width:70px;">Ctr CL</th>
                    <th class="center" style="width:60px;">Usage CL</th>
                    <th class="center" style="width:65px;">Tgl MRC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($machines as $i => $machine)
                    @php
                        $log = $mrcLogs[$machine->id] ?? null;
                        $sudahMrc = $log !== null;
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $machine->customer?->nama_customer ?? '-' }}</td>
                        <td>{{ $machine->serial_number }}</td>
                        <td>{{ $machine->tipe_model }}</td>
                        <td>{{ $machine->customer?->technician?->nama_technician ?? '-' }}</td>
                        <td class="center">
                            @if ($sudahMrc)
                                <span class="status-sudah">✅ Sudah</span>
                            @else
                                <span class="status-belum">❌ Belum</span>
                            @endif
                        </td>
                        <td class="center">{{ $sudahMrc ? number_format($log->counter_bw) : '-' }}</td>
                        <td class="center">{{ $sudahMrc ? number_format($log->usage_bw) : '-' }}</td>
                        <td class="center">{{ $sudahMrc ? number_format($log->counter_color) : '-' }}</td>
                        <td class="center">{{ $sudahMrc ? number_format($log->usage_color) : '-' }}</td>
                        <td class="center">
                            {{ $sudahMrc ? \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Total</td>
                    <td class="center">{{ $totalMesin }} mesin</td>
                    <td class="center">
                        {{ number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->counter_bw)) }}
                    </td>
                    <td class="center">
                        {{ number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->usage_bw)) }}
                    </td>
                    <td class="center">
                        {{ number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->counter_color)) }}
                    </td>
                    <td class="center">
                        {{ number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->usage_color)) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Kepala Cabang</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
            <div class="ttd">
                <p>Bandung, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p>Dibuat oleh,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>
    </div>

</body>

</html>
