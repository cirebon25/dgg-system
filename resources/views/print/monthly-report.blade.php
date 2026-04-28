<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan DGG - {{ $month }}/{{ $year }}</title>
    <style>
        /* PAKSA LANDSCAPE SAAT PRINT */
        @page { size: landscape; margin: 1cm; }

        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f2f2f2; text-align: center; }

        /* WARNA BADGE */
        .badge {
            padding: 3px 6px; border-radius: 4px; font-weight: bold; color: white;
            text-align: center; display: inline-block;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .bg-rn { background-color: #22c55e !important; }
        .bg-cm { background-color: #ef4444 !important; }
        .bg-rm { background-color: #3b82f6 !important; }
        .bg-rr { background-color: #b45309 !important; }
        .bg-jk { background-color: #a855f7 !important; }
        .bg-l  { background-color: #6b7280 !important; }

        /* WARNA USAGE */
        .text-blue { color: #3b82f6 !important; font-weight: bold; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-red { color: #ef4444 !important; font-weight: bold; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .no-print { margin-bottom: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #22c55e; color: white; border: none; cursor: pointer; border-radius: 5px;">
            🖨️ CETAK SEKARANG
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">
            ⬅️ KEMBALI
        </button>
    </div>

    <div class="header">
        <h2 style="margin: 0;">LAPORAN BULANAN SERVICE MESIN FOTOCOPY</h2>
        <h3 style="margin: 5px 0;">Periode: Bulan {{ $month }} Tahun {{ $year }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">Tgl</th>
                <th width="16%">Customer / Model / Pasang</th>
                <th width="10%">SN Mesin</th>
                <th width="8%">Counter (BW/CL)</th>
                <th width="8%">Usage (BW/CL)</th>
                <th width="5%">Tipe</th>
                <th width="18%">Perbaikan</th>
                <th width="16%">Sparepart</th>
                <th width="14%">Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                
                <td>
                    <strong>{{ $log->machine->deployment->customer->nama_customer ?? '-' }}</strong><br>
                    Model: {{ $log->machine->model_mesin ?? '-' }}<br>
                    Instal: {{ $log->machine->deployment?->tanggal_instal ? \Carbon\Carbon::parse($log->machine->deployment->tanggal_instal)->format('d/m/Y') : '-' }}
                </td>
                
                <td>{{ $log->machine->serial_number ?? '-' }}</td>
                
                <td>
                    BW: {{ number_format($log->counter_bw) }}<br>
                    CL: {{ number_format($log->counter_color) }}
                </td>
                
                <td>
                    <span class="text-blue">BW: {{ number_format($log->usage_bw) }}</span><br>
                    <span class="text-red">CL: {{ number_format($log->usage_color) }}</span>
                </td>
                
                <td style="text-align: center;">
                    @php
                        $colorClass = match($log->tipe_kunjungan) {
                            'RN' => 'bg-rn', 'CM' => 'bg-cm', 'RM' => 'bg-rm',
                            'RR' => 'bg-rr', 'JK' => 'bg-jk', default => 'bg-l',
                        };
                    @endphp
                    <span class="badge {{ $colorClass }}">{{ $log->tipe_kunjungan }}</span>
                </td>
                
                <td>{{ $log->perbaikan }}</td>
                
                <td>
                    @if($log->serviceLogSpareparts && $log->serviceLogSpareparts->count() > 0)
                        <ul style="margin: 0; padding-left: 15px;">
                            @foreach($log->serviceLogSpareparts as $sp)
                                <li>{{ $sp->sparepart->nama_sparepart ?? 'Tidak Diketahui' }} ({{ $sp->jumlah }})</li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </td>
                
                <td>
                    {{ $log->technician->nama_technician ?? '-' }}
                    @if($log->nama_teknisi_manual)
                        <br><small>Partner: {{ $log->nama_teknisi_manual }}</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>