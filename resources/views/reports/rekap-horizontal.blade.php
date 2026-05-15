<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Horizontal Per Rayon - DGG System</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 9px; margin: 0; padding: 15px; color: #222; }
        header { text-align: center; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        
        .rayon-title { background: #000; color: #fff; padding: 8px; font-weight: bold; font-size: 12px; margin-top: 20px; text-transform: uppercase; }
        
        /* Tabel Statistik */
        .stat-table { width: 35%; margin: 10px 0; border-collapse: collapse; }
        .stat-table th, .stat-table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .stat-table th { background: #eee; font-size: 8px; }

        /* Tabel Utama */
        table.main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th { background: #2c3e50; color: white; border: 1px solid #000; padding: 6px; font-size: 8px; }
        .main-table td { border: 1px solid #000; padding: 5px; vertical-align: top; word-wrap: break-word; }

        /* Styling Konten */
        .bold { font-weight: bold; color: #000; }
        .sub-text { font-size: 8px; color: #555; display: block; margin-top: 2px; }
        .sn-badge { background: #f1f1f1; border: 1px solid #ccc; padding: 2px; font-weight: bold; display: inline-block; margin-top: 3px; }
        
        .visit-box { min-height: 110px; }
        .v-head { border-bottom: 1px solid #ddd; margin-bottom: 3px; display: flex; justify-content: space-between; font-weight: bold; }
        .v-counter { background: #fffbe6; border: 1px solid #ffe58f; padding: 2px; font-size: 8px; margin-bottom: 3px; }
        .v-detail { background: #e6f7ff; border: 1px solid #91d5ff; padding: 3px; font-size: 8px; font-style: italic; border-radius: 2px; }
        .v-parts { color: #d4380d; font-size: 7.5px; margin-top: 3px; padding-left: 10px; }
        .v-tech { font-size: 7px; color: #777; margin-top: 4px; border-top: 1px dashed #ccc; padding-top: 2px; }

        @media print { @page { size: landscape; margin: 8mm; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <header>
        <h1 style="margin:0;">PT DINAMIKA GLOBAL GEMILANG</h1>
        <h2 style="margin:5px 0;">LAPORAN MONITORING UNIT & HISTORI SERVIS</h2>
        <p>Periode: {{ Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</p>
    </header>

    @foreach($rayons as $rayon)
        @php
            $allMachines = $rayon->customers->flatMap->machines;
            $allLogs = $allMachines->flatMap->serviceLogs;
            $stat = [
                'RM' => $allLogs->where('tipe_kunjungan', 'RM')->count(),
                'CM' => $allLogs->where('tipe_kunjungan', 'CM')->count(),
                'RN' => $allLogs->where('tipe_kunjungan', 'RN')->count(),
                'RR' => $allLogs->where('tipe_kunjungan', 'RR')->count(),
            ];
        @endphp

        <div class="rayon-title">📍 RAYON: {{ $rayon->nama_rayon }}</div>
        
        <table class="stat-table">
            <thead>
                <tr>
                    <th>RM</th><th>CM</th><th>RN</th><th>RR</th><th>TOTAL UNIT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $stat['RM'] }}</td><td>{{ $stat['CM'] }}</td>
                    <td>{{ $stat['RN'] }}</td><td>{{ $stat['RR'] }}</td>
                    <td><b>{{ $allMachines->count() }}</b></td>
                </tr>
            </tbody>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="20">NO</th>
                    <th width="140">CUSTOMER & KONTAK</th>
                    <th width="110">SN & MODEL UNIT</th>
                    <th>KUNJUNGAN 1</th>
                    <th>KUNJUNGAN 2</th>
                    <th>KUNJUNGAN 3</th>
                    <th>KUNJUNGAN 4</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($rayon->customers as $customer)
                    @foreach($customer->machines as $m)
                        <tr>
                            <td style="text-align:center;"><b>{{ $no++ }}</b></td>
                            <td>
                                <span class="bold">{{ $customer->nama_customer }}</span>
                                <span class="sub-text">📞 {{ $customer->no_telp ?? $customer->phone ?? '-' }}</span>
                                <span class="sub-text">📍 {{ $customer->kota ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="sn-badge">SN: {{ $m->serial_number }}</div>
                                <span class="sub-text"><b>Model:</b> {{ $m->tipe_model }}</span>
                                <span class="sub-text"><b>Lokasi:</b> {{ $m->lokasi_mesin ?? '-' }}</span>
                            </td>

                            @php $logs = $m->serviceLogs; @endphp
                            @for($i = 0; $i < 4; $i++)
                                <td>
                                    @if(isset($logs[$i]))
                                        @php $log = $logs[$i]; @endphp
                                        <div class="visit-box">
                                            <div class="v-head">
                                                <span>{{ $log->tanggal->format('d/m/y') }}</span>
                                                <span style="color:red;">[{{ $log->tipe_kunjungan }}]</span>
                                            </div>
                                            <div class="v-counter">
                                                B: {{ number_format($log->counter_bw) }} | C: {{ number_format($log->counter_color) }}
                                            </div>
                                            <div class="v-detail">
                                                <b>K:</b> {{ Str::limit($log->kerusakan, 35) }}<br>
                                                <b>T:</b> {{ Str::limit($log->perbaikan, 45) }}
                                            </div>
                                            @if($log->serviceLogSpareparts->count() > 0)
                                                <div class="v-parts">
                                                    <b>Part:</b> 
                                                    @foreach($log->serviceLogSpareparts as $sp)
                                                        {{ $sp->sparepart->nama_sparepart }} ({{ $sp->jumlah }}),
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="v-tech">
                                                👤 {{ $log->technician->nama_technician ?? '-' }}
                                                @if($log->nama_teknisi_2) / {{ $log->nama_teknisi_2 }} @endif
                                            </div>
                                        </div>
                                    @else
                                        <div style="text-align:center; color:#ccc; padding-top:45px;">-</div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>