<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional DGG</title>
    <style>
        /* Pengaturan Dasar */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 10px; /* Ukuran font sedikit dikecilkan karena kolom bertambah */
            margin: 0; 
            padding: 15px 20px;
            color: #333; 
        }

        /* Teks Kecil di Pojok Kanan Atas */
        .title-atas-kanan {
            text-align: right;
            font-size: 10px;
            color: #555;
            margin-bottom: 5px;
            font-style: italic;
            margin-right: 460px; 
        }

        /* HEADER UTAMA */
        .header { 
            width: 100%; 
            display: block;
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h2 { 
            margin: 0; 
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 18px;
        }
        .header p { 
            margin: 5px 0 0 0; 
            font-size: 12px; 
            font-weight: bold;
        }

        /* Tabel Laporan */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 5px 3px; 
            text-align: center; 
            word-wrap: break-word; 
            vertical-align: middle;
        }
        th { 
            background-color: #f2f2f2 !important; 
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            -webkit-print-color-adjust: exact; 
        }
        
        /* Warna Badge */
        .badge { 
            padding: 2px 4px; 
            border-radius: 3px; 
            font-weight: bold; 
            color: white !important;
            display: inline-block;
            font-size: 9px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .bg-success { background-color: #16a34a !important; }
        .bg-info { background-color: #2563eb !important; }
        .bg-danger { background-color: #dc2626 !important; }
        .bg-warning { background-color: #d97706 !important; }
        .bg-gray { background-color: #4b5563 !important; }

        /* Tanda Tangan */
        .footer { margin-top: 30px; width: 100%; }
        .ttd-container { float: right; width: 220px; text-align: center; }

        /* Pengaturan Cetak */
        @media print {
            @page { 
                size: landscape; 
                margin: 0.8cm; 
            }
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="title-atas-kanan">
        Laporan Operasional DGG Cirebon
    </div>

    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <p>LAPORAN PENGERJAAN UNIT & MAINTENANCE</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 80px;">SN Mesin</th>
                <th style="width: 160px;">Customer</th>
                <th style="width: 35px;">Tipe</th>
                <th style="width: 90px;">Counter Akhir</th>
                <th style="width: 80px;">Usage (Lbr)</th>
                <th style="width: 100px;">Sparepart</th>
                <th style="width: 100px;">Tindakan / Perbaikan</th>
                <th style="width: 75px;">Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $log->machine->serial_number }}</td>
                <td style="text-align: left;">
                    <strong>{{ $log->machine->deployments->first()?->customer->nama_customer ?? 'Unit Standby' }}</strong>
                </td>
                <td>
                    <span class="badge 
                        {{ $log->tipe_kunjungan == 'RN' ? 'bg-success' : '' }}
                        {{ $log->tipe_kunjungan == 'RM' ? 'bg-info' : '' }}
                        {{ $log->tipe_kunjungan == 'CM' ? 'bg-danger' : '' }}
                        {{ $log->tipe_kunjungan == 'TN' ? 'bg-warning' : '' }}
                        {{ !in_array($log->tipe_kunjungan, ['RN','RM','CM','TN']) ? 'bg-gray' : '' }}">
                        {{ $log->tipe_kunjungan }}
                    </span>
                </td>
                <td>
                    C: {{ number_format($log->counter_color) }}<br>
                    B: {{ number_format($log->counter_bw) }}
                </td>
                <td>
                    C: {{ number_format($log->usage_color) }}<br>
                    B: {{ number_format($log->usage_bw) }}
                </td>
                <td style="text-align: left;">
                    @if($log->sparepart) 
                        {{ $log->sparepart->nama_sparepart }} <br>
                        <small>Jml: {{ $log->jumlah_sparepart }}</small>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: left;">
                    {{ $log->perbaikan }}
                </td>
                <td>{{ $log->technician->nama_technician }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd-container">
            <p>Cirebon, {{ date('d F Y') }}</p>
            <p>Admin Operasional,</p>
            <br><br><br><br>
            <p><strong>( ________________________ )</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            🖨️ CETAK LAPORAN SEKARANG
        </button>
    </div>

</body>
</html>