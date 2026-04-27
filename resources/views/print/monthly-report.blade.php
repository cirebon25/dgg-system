<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan DGG - {{ $month }}/{{ $year }}</title>
    <style>
        /* PAKSA LANDSCAPE SAAT PRINT */
        @page { 
            size: landscape; 
            margin: 1cm; 
        }

        body { 
            font-family: sans-serif; 
            font-size: 11px; 
            margin: 0;
            padding: 20px;
        }

        .header { text-align: center; margin-bottom: 20px; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        th, td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left; 
        }

        th { background: #f2f2f2; }

        /* WARNA BADGE (Tetap muncul saat di-print) */
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
            text-align: center;
            display: inline-block;
            -webkit-print-color-adjust: exact; /* Agar warna muncul di print */
            print-color-adjust: exact;
        }

        .bg-rn { background-color: #22c55e !important; } /* Hijau */
        .bg-cm { background-color: #ef4444 !important; } /* Merah */
        .bg-rm { background-color: #3b82f6 !important; } /* Biru */
        .bg-rr { background-color: #b45309 !important; } /* Coklat/Amber */
        .bg-jk { background-color: #a855f7 !important; } /* Ungu */
        .bg-l  { background-color: #6b7280 !important; } /* Abu-abu */

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
        <h3 style="margin: 5px 0;">Periode: {{ $month }} / {{ $year }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tgl</th>
                <th>Customer</th>
                <th>Model</th>
                <th>SN Mesin</th>
                <th>Usage BW</th>
                <th>Usage Color</th>
                <th style="text-align: center;">Tipe</th>
                <th>Perbaikan</th>
                <th>Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $log->machine->customer->nama_customer ?? '-' }}</td>
                <td>{{ $log->machine->model_mesin }}</td>
                <td>{{ $log->machine->serial_number }}</td>
                <td>{{ number_format($log->usage_bw) }}</td>
                <td>{{ number_format($log->usage_color) }}</td>
                <td style="text-align: center;">
                    @php
                        $colorClass = match($log->tipe_kunjungan) {
                            'RN' => 'bg-rn',
                            'CM' => 'bg-cm',
                            'RM' => 'bg-rm',
                            'RR' => 'bg-rr',
                            'JK' => 'bg-jk',
                            default => 'bg-l',
                        };
                    @endphp
                    <span class="badge {{ $colorClass }}">
                        {{ $log->tipe_kunjungan }}
                    </span>
                </td>
                <td>{{ $log->perbaikan }}</td>
                <td>
                    {{ $log->technician->nama_technician }}
                    @if($log->nama_teknisi_manual)
                        / {{ $log->nama_teknisi_manual }}
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