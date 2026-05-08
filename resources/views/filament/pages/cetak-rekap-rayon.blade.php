<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional DGG</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 9px; 
            margin: 0; 
            padding: 15px 20px;
            color: #333; 
        }
        .title-atas-kanan {
            text-align: right; font-size: 9px; color: #555; font-style: italic;
        }
        .header { 
            width: 100%; text-align: center; margin-bottom: 20px; 
            border-bottom: 3px double #000; padding-bottom: 10px;
        }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 5px 0 0 0; font-size: 12px; font-weight: bold; }

        .rayon-header {
            background-color: #74e969; color: white; padding: 6px 10px;
            font-weight: bold; font-size: 11px; margin-top: 20px;
            text-transform: uppercase; -webkit-print-color-adjust: exact;
        }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px 3px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2 !important; font-size: 8px; text-transform: uppercase; -webkit-print-color-adjust: exact; }
        
        /* Badge Status */
        .badge { padding: 2px 4px; border-radius: 3px; font-weight: bold; color: white !important; display: inline-block; font-size: 8px; -webkit-print-color-adjust: exact; }
        .bg-success { background-color: #16a34a !important; }
        .bg-info { background-color: #2563eb !important; }
        .bg-danger { background-color: #dc2626 !important; }
        .bg-warning { background-color: #d97706 !important; }
        .bg-gray { background-color: #4b5563 !important; }

        .footer { margin-top: 30px; width: 100%; }
        .ttd-container { float: right; width: 220px; text-align: center; }
        .text-left { text-align: left; }

        @media print {
            @page { size: landscape; margin: 0.8cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="title-atas-kanan">DGG Cirebon | Periode: {{ $namaBulan }} {{ $year }}</div>

    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <p>LAPORAN PENGERJAAN UNIT & MAINTENANCE PER RAYON</p>
    </div>

    @foreach($rayons as $rayon)
        <div class="rayon-header"> RAYON: {{ strtoupper($rayon->nama_rayon) }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 60px;">Tanggal</th>
                    <th style="width: 80px;">No. Kontrak</th>
                    <th style="width: 70px;">SN Mesin</th>
                    <th style="width: 130px;">Customer</th>
                    <th style="width: 30px;">Tipe</th>
                    <th style="width: 80px;">Counter Akhir</th>
                    <th style="width: 70px;">Usage (Lbr)</th>
                    <th style="width: 90px;">Sparepart</th>
                    <th>Tindakan / Perbaikan</th>
                    <th style="width: 70px;">Teknisi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($rayon->customers as $customer)
                    @foreach($customer->deployments as $dep)
                        @php
                            $logs = \App\Models\ServiceLog::where('machine_id', $dep->machine_id)
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year)
                                ->with(['technician', 'serviceLogSpareparts.sparepart'])
                                ->get();
                        @endphp

                        @if($logs->isEmpty())
                            {{-- BARIS UNIT NORMAL (TIDAK BOLEH PAKAI VARIABLE $log DI SINI) --}}
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>-</td>
                                <td>{{ $dep->no_kontrak ?? '-' }}</td>
                                <td>{{ $dep->machine->serial_number }}</td>
                                <td class="text-left"><strong>{{ $customer->nama_customer }}</strong></td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td style="color: #16a34a; font-style: italic;"> </td>
                                <td>-</td>
                            </tr>
                        @else
                            {{-- BARIS ADA SERVICE (VARIABLE $log HANYA ADA DI SINI) --}}
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $dep->no_kontrak ?? '-' }}</td>
                                <td><strong>{{ $log->machine->serial_number }}</strong></td>
                                <td class="text-left"><strong>{{ $customer->nama_customer }}</strong></td>
                                <td>
                                    <span class="badge 
                                        {{ $log->tipe_kunjungan == 'RN' ? 'bg-success' : '' }}
                                        {{ $log->tipe_kunjungan == 'RM' ? 'bg-info' : '' }}
                                        {{ $log->tipe_kunjungan == 'CM' ? 'bg-danger' : '' }}
                                        {{ $log->tipe_kunjungan == 'RR' ? 'bg-warning' : '' }}
                                        {{ !in_array($log->tipe_kunjungan, ['RN','RM','CM','RR']) ? 'bg-gray' : '' }}">
                                        {{ $log->tipe_kunjungan }}
                                    </span>
                                </td>
                                {{-- WARNA FONT COUNTER --}}
                                <td>
                                    <span style="color: #e11d48;">CL: {{ number_format($log->counter_color) }}</span><br>
                                    <span style="color: #2563eb;">BW: {{ number_format($log->counter_bw) }}</span>
                                </td>
                                {{-- WARNA FONT USAGE --}}
                                <td>
                                    <strong style="color: #e11d48;">CL: {{ number_format($log->usage_color) }}</strong><br>
                                    <strong style="color: #2563eb;">BW: {{ number_format($log->usage_bw) }}</strong>
                                </td>
                                <td class="text-left">
                                    @foreach($log->serviceLogSpareparts as $sp)
                                        • {{ $sp->sparepart->nama_sparepart }} ({{ $sp->jumlah }})<br>
                                    @endforeach
                                </td>
                                <td class="text-left">{{ $log->perbaikan }}</td>
                                <td>{{ $log->technician->nama_technician }}</td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        <div class="ttd-container">
            <p>Cirebon, {{ date('d F Y') }}</p>
            <p>Admin Operasional,</p>
            <br><br><br>
            <p><strong>( ________________________ )</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>