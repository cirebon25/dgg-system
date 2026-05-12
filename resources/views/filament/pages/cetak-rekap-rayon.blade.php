<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional DGG - {{ $namaBulan }} {{ $year }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 9px; margin: 0; padding: 15px 20px; color: #333; }
        .header { width: 100%; text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; }
        
        .rayon-header { 
            background-color: #1e293b; color: white; padding: 8px 12px; 
            font-weight: bold; font-size: 11px; margin-top: 20px; 
            display: flex; justify-content: space-between; align-items: center; 
            text-transform: uppercase; -webkit-print-color-adjust: exact; 
        }
        .rayon-meta { color: #facc15; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px; }
        th, td { border: 1px solid #000; padding: 5px 3px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2 !important; font-size: 8px; text-transform: uppercase; -webkit-print-color-adjust: exact; }
        
        .badge { padding: 2px 4px; border-radius: 3px; font-weight: bold; color: white !important; display: inline-block; font-size: 8px; -webkit-print-color-adjust: exact; }
        .bg-cm { background-color: #991b1b !important; } /* Merah Tua */
        .bg-rn { background-color: #16a34a !important; } /* Hijau */
        .bg-rm { background-color: #2563eb !important; } /* Biru */
        .bg-tn { background-color: #eab308 !important; color: #000 !important; } /* Kuning (Teks Hitam) */
        .bg-jk { background-color: #9333ea !important; } /* Ungu */
        .bg-l  { background-color: #f97316 !important; } /* Oren */
        .bg-rr { background-color: #eb0b0b !important; color: #e9ec0c !important; } /* Merah Muda */
        .bg-gray { background-color: #4b5563 !important; } /* Default */

        @media print { 
            @page { size: landscape; margin: 0.8cm; } 
            .rayon-box { page-break-inside: avoid; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <p>LAPORAN PENGERJAAN UNIT & MAINTENANCE PER RAYON ({{ $namaBulan }} {{ $year }})</p>
    </div>

    @foreach($rayons as $rayon)
        @php
            $totalUnit = 0;
            foreach($rayon->customers as $c) { $totalUnit += $c->deployments->count(); }
        @endphp

        <div class="rayon-box">
            <div class="rayon-header">
                <span>📍 RAYON: {{ strtoupper($rayon->nama_rayon) }}</span>
                <span class="rayon-meta">👨‍🔧 TIM: {{ $rayon->technicians->pluck('nama_technician')->implode(', ') }} | 📟 POPULASI: {{ $totalUnit }} UNIT</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 60px;">Tanggal</th>
                        <th style="width: 70px;">SN Mesin</th>
                        <th style="width: 130px;">Customer</th>
                        <th style="width: 35px;">Tipe</th>
                        <th style="width: 80px;">Counter Akhir</th>
                        <th style="width: 75px;">Usage</th>
                        <th style="width: 90px;">Sparepart</th>
                        <th>Perbaikan</th>
                        <th style="width: 85px;">Teknisi Log</th>
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
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>-</td>
                                    <td>{{ $dep->machine->serial_number }}</td>
                                    <td style="text-align:left;"><strong>{{ $customer->nama_customer }}</strong></td>
                                    <td colspan="5" style="color: #16a34a; font-style: italic;">Unit Standby / Normal</td>
                                    <td>-</td>
                                </tr>
                            @else
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $log->tanggal->format('d/m/y') }}</td>
                                    <td>{{ $log->machine->serial_number }}</td>
                                    <td style="text-align:left;"><strong>{{ $customer->nama_customer }}</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($log->tipe_kunjungan == 'CM') bg-cm
                                            @elseif($log->tipe_kunjungan == 'RN') bg-rn
                                            @elseif($log->tipe_kunjungan == 'RM') bg-rm
                                            @elseif($log->tipe_kunjungan == 'TN') bg-tn
                                            @elseif($log->tipe_kunjungan == 'JK') bg-jk
                                            @elseif($log->tipe_kunjungan == 'L')  bg-l
                                            @elseif($log->tipe_kunjungan == 'RR') bg-rr
                                            @else bg-gray @endif">
                                            {{ $log->tipe_kunjungan }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color: #e11d48; font-weight: bold;">CL: {{ number_format($log->counter_color) }}</span><br>
                                        <span style="color: #0b0b0c; font-weight: bold;">BW: {{ number_format($log->counter_bw) }}</span>
                                    </td>
                                    <td>
                                        <span style="color: #e11d48; font-weight: bold;">CL: {{ number_format($log->usage_color) }}</span><br>
                                        <span style="color: #161618; font-weight: bold;">BW: {{ number_format($log->usage_bw) }}</span>
                                    </td>
                                    <td style="text-align:left;">@foreach($log->serviceLogSpareparts as $sp) • {{ $sp->sparepart->nama_sparepart }}<br> @endforeach</td>
                                    <td style="text-align:left;">{{ $log->perbaikan }}</td>
                                    <td>
                                        {{-- TAMPILKAN DUET MAUT TEKNISI (RELASI + MANUAL) --}}
                                        <b>{{ $log->technician->nama_technician ?? '-' }}</b>
                                        @if($log->nama_teknisi_2)
                                            <br><span style="font-size: 8px; color: #555;">& {{ $log->nama_teknisi_2 }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>