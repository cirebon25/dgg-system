<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Horizontal Per Rayon - DGG System</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 10px;
            color: #222;
        }

        header {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .rayon-title {
            background: #000;
            color: #fff;
            padding: 6px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            text-transform: uppercase;
        }

        /* Tabel Statistik Horizontal - Full Warna Biru, Font Putih */
        .stat-table {
            width: 60%;
            margin: 15px 0 25px 0;
            border-collapse: collapse;
        }

        .stat-table th,
        .stat-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .stat-table th {
            background: #002766;
            color: white;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .stat-table td {
            background: #0050b3;
            color: white;
            font-weight: bold;
            font-size: 8.5px;
        }

        /* Tabel Utama */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Header Utama FULL Hijau */
        .main-table th {
            background: #1b5e20;
            color: white;
            border: 1px solid #000;
            padding: 4px 1px;
            font-size: 7px;
            text-align: center;
        }

        .main-table td {
            border: 1px solid #000;
            padding: 4px 1px;
            vertical-align: middle;
            font-size: 7.5px;
            word-wrap: break-word;
            text-align: center;
        }

        .text-left {
            text-align: left;
            padding-left: 3px !important;
        }

        /* Baris Pemisah Kota Dalam Tabel */
        .row-kota {
            background-color: #eceff1 !important;
            font-weight: bold;
            padding: 5px;
            font-size: 8.5px;
            color: #1a237e;
            text-align: left !important;
        }

        /* Background Kolom Spesifik */
        .bg-seri {
            background-color: #fffb8f !important;
            color: #000 !important;
        }

        /* Kuning */
        .bg-kontrak {
            background-color: #f6ffed !important;
            color: #1b5e20 !important;
            font-weight: bold;
        }

        /* Hijau */

        /* CSS Pewarnaan Kode Tanggal (Warna Pastel) */
        .bg-rm {
            background-color: #072cfa !important;
            color: #f0f2f5 !important;
            font-weight: bold;
        }

        .bg-cm {
            background-color: #f71a06 !important;
            color: #f8f0f0 !important;
            font-weight: bold;
        }

        .bg-rn {
            background-color: #09f043 !important;
            color: #edf3eb !important;
            font-weight: bold;
        }

        .bg-rr {
            background-color: #fff7e6 !important;
            color: #d46b08 !important;
            font-weight: bold;
        }

        .bg-jk {
            background-color: #f5e8df !important;
            color: #614700 !important;
            font-weight: bold;
        }

        .bg-l {
            background-color: #f9f0ff !important;
            color: #531dab !important;
            font-weight: bold;
        }

        .bg-tn {
            background-color: #f5f5f5 !important;
            color: #595959 !important;
            font-weight: bold;
        }

        /* CSS Pewarnaan Keterangan Akhir */
        .bg-ket-blm {
            background-color: #fff1f0 !important;
            color: #cf1322 !important;
            font-weight: bold;
            text-transform: uppercase;
        }

        .bg-ket-sudah {
            background-color: #e6f7ff !important;
            color: #0050b3 !important;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* REVISI: Class CSS untuk memisahkan halaman saat diprint */
        .page-break {
            page-break-after: always;
            break-after: page;

            /* Pewarnaan Khusus Tabel Statistik Bawah */
            .stat-rm {
                background-color: #3205F8 !important;
                color: #fffff !important;
                font-weight: bold;
            }

            /* Biru */
            .stat-cm {
                background-color: #f80505 !important;
                color: #e4dadb !important;
                font-weight: bold;
            }

            /* Merah */
            .stat-rn {
                background-color: #f6ffed !important;
                color: #389e0d !important;
                font-weight: bold;
            }

            /* Hijau (Untuk TN/RN) */
            .stat-rr {
                background-color: #f6ffed !important;
                color: #389e0d !important;
                font-weight: bold;
            }

            /* Hijau */
            .stat-blm {
                background-color: #fff0f6 !important;
                color: #c41d7f !important;
                font-weight: bold;
            }

            /* Pink */
            .stat-sudah {
                background-color: #e6f7ff !important;
                color: #0050b3 !important;
                font-weight: bold;
            }

            /* Biru */
            .stat-total {
                background-color: #ffffff !important;
                color: #222222 !important;
                font-weight: bold;
            }

            /* Putih */
        }

        @media print {
            @page {
                size: landscape;
                margin: 4mm;
            }

            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <header>
        <h1 style="margin:0; font-size: 16px;">PT DINAMIKA GLOBAL GEMILANG</h1>
        <h2 style="margin:3px 0; font-size: 12px;">LAPORAN MONITORING UNIT & HISTORI SERVIS</h2>
        <p style="margin:0; font-size: 10px;">Periode:
            {{ Carbon\Carbon::create()->year($year)->month($month)->translatedFormat('F') }} {{ $year }}</p>
    </header>

    @foreach ($rayons as $rayon)
        @php
            $deployments = \App\Models\Deployment::with([
                'customer',
                'machine.serviceLogs' => function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->orderBy('tanggal', 'asc');
                },
            ])
                ->whereHas('customer', function ($q) use ($rayon) {
                    $q->where('rayon_id', $rayon->id);
                })
                ->get();

            $allLogs = $deployments->flatMap(function ($dep) {
                return $dep->machine ? $dep->machine->serviceLogs : collect();
            });

            // Hitung status Sudah RM / Belum RM per unit mesin
            $sudahRmCount = 0;
            $belumRmCount = 0;
            foreach ($deployments as $dep) {
                if ($dep->customer && $dep->machine) {
                    $hasRM = $dep->machine->serviceLogs->contains(
                        fn($log) => strtoupper($log->tipe_kunjungan) === 'RM',
                    );
                    $hasRM ? $sudahRmCount++ : $belumRmCount++;
                }
            }

            $stat = [
                'RM' => $allLogs->where('tipe_kunjungan', 'RM')->count(),
                'CM' => $allLogs->where('tipe_kunjungan', 'CM')->count(),
                'TN' => $allLogs->where('tipe_kunjungan', 'TN')->count(),
                'RR' => $allLogs->where('tipe_kunjungan', 'RR')->count(),
                'SUDAH_RM' => $sudahRmCount,
                'BELUM_RM' => $belumRmCount,
                'TOTAL_MESIN' => $deployments->whereNotNull('machine_id')->count(),
            ];

            // Pisahkan berdasarkan KOTA customer
            $deploymentsByKota = $deployments->groupBy(function ($dep) {
                return $dep->customer->kota ? strtoupper($dep->customer->kota) : 'TANPA KOTA';
            });
        @endphp

        <div class="{{ !$loop->last ? 'page-break' : '' }}">

            <div class="rayon-title">📍 RAYON: {{ $rayon->nama_rayon }}</div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th rowspan="2" width="18">NO</th>
                        <th rowspan="2" width="100">NAMA CUSTOMER</th>
                        <th rowspan="2" width="70">TIPE MESIN</th>
                        <th rowspan="2" width="65">NO SERI</th>
                        <th rowspan="2" width="45">TGL PASANG</th>
                        <th colspan="31">TANGGAL KUNJUNGAN (1 SD 31)</th>
                        <th rowspan="2" width="85">COUNTER AKHIR</th>
                        <th rowspan="2" width="85">PERBAIKAN</th>
                        <th colspan="2" width="120">TEKNISI</th>
                        <th rowspan="2" width="60">NO KONTRAK</th>
                        <th rowspan="2" width="65">KETERANGAN</th>
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= 31; $i++)
                            <th width="18" style="font-size: 6.5px; padding: 1px; white-space: nowrap;">
                                {{ $i }}</th>
                        @endfor
                        <th width="60">TEKNISI 1</th>
                        <th width="60">TEKNISI 2</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp

                    @foreach ($deploymentsByKota as $kota => $itemsInKota)
                        <tr>
                            <td colspan="42" class="row-kota">
                                🏙️ KOTA / KABUPATEN: {{ $kota }}
                            </td>
                        </tr>

                        @foreach ($itemsInKota as $dep)
                            @if ($dep->customer && $dep->machine)
                                @php
                                    $customer = $dep->customer;
                                    $m = $dep->machine;
                                    $logs = $m->serviceLogs;

                                    $bwDisplay = '-';
                                    $colorDisplay = '-';
                                    $teknisi1Display = '-';
                                    $teknisi2Display = '-';
                                    $perbaikanDisplay = '-';

                                    if ($logs->isNotEmpty()) {
                                        if ($logs->count() > 5) {
                                            $bwDisplay =
                                                number_format($logs->first()->counter_bw) .
                                                ' ... ' .
                                                number_format($logs->last()->counter_bw);
                                            $colorDisplay =
                                                number_format($logs->first()->counter_color ?? 0) .
                                                ' ... ' .
                                                number_format($logs->last()->counter_color ?? 0);
                                        } else {
                                            $bwDisplay = $logs
                                                ->map(fn($log) => number_format($log->counter_bw))
                                                ->implode(' | ');
                                            $colorDisplay = $logs
                                                ->map(fn($log) => number_format($log->counter_color ?? 0))
                                                ->implode(' | ');
                                        }

                                        $teknisi1Display = $logs
                                            ->map(function ($log) {
                                                $nama1 = $log->technician?->nama_technician ?? '-';
                                                $tgl = \Carbon\Carbon::parse($log->tanggal)->day;
                                                return "{$nama1}/{$tgl}";
                                            })
                                            ->unique()
                                            ->implode(' | ');

                                        $teknisi2Display = $logs
                                            ->map(function ($log) {
                                                $tgl = \Carbon\Carbon::parse($log->tanggal)->day;
                                                $nama2 = $log->nama_teknisi_2;
                                                return !empty($nama2) && $nama2 !== '-' ? "{$nama2}/{$tgl}" : null;
                                            })
                                            ->filter()
                                            ->unique()
                                            ->implode(' | ');

                                        if (empty($teknisi2Display)) {
                                            $teknisi2Display = '-';
                                        }

                                        $perbaikanDisplay = $logs
                                            ->map(fn($log) => $log->perbaikan ? Str::limit($log->perbaikan, 35) : '-')
                                            ->implode(' | ');
                                    }

                                    $hasRM = $logs->contains(fn($log) => strtoupper($log->tipe_kunjungan) === 'RM');
                                    $textKeterangan = $hasRM ? 'sudah rm' : 'blm rm';
                                    $classKeterangan = $hasRM ? 'bg-ket-sudah' : 'bg-ket-blm';
                                @endphp
                                <tr>
                                    <td><b>{{ $no++ }}</b></td>
                                    <td class="text-left bold">{{ $customer->nama_customer }}</td>
                                    <td>{{ $m->tipe_model }}</td>
                                    <td class="bold bg-seri">{{ $m->serial_number }}</td>
                                    <td>{{ $dep->tgl_pasang ? \Carbon\Carbon::parse($dep->tgl_pasang)->format('d/m/y') : ($m->tgl_pasang ? \Carbon\Carbon::parse($m->tgl_pasang)->format('d/m/y') : '-') }}
                                    </td>

                                    @for ($day = 1; $day <= 31; $day++)
                                        @php
                                            $logHariIni = $logs->first(
                                                fn($log) => \Carbon\Carbon::parse($log->tanggal)->day == $day,
                                            );
                                            $bgClass = '';
                                            if ($logHariIni) {
                                                switch (strtoupper($logHariIni->tipe_kunjungan)) {
                                                    case 'RM':
                                                        $bgClass = 'bg-rm';
                                                        break;
                                                    case 'CM':
                                                        $bgClass = 'bg-cm';
                                                        break;
                                                    case 'RN':
                                                        $bgClass = 'bg-rn';
                                                        break;
                                                    case 'RR':
                                                        $bgClass = 'bg-rr';
                                                        break;
                                                    case 'JK':
                                                        $bgClass = 'bg-jk';
                                                        break;
                                                    case 'L':
                                                        $bgClass = 'bg-l';
                                                        break;
                                                    case 'TN':
                                                        $bgClass = 'bg-tn';
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <td class="{{ $bgClass }}"
                                            style="font-size: 6.5px; font-weight: bold; white-space: nowrap; padding: 2px 0;">
                                            {{ $logHariIni ? $logHariIni->tipe_kunjungan : '' }}
                                        </td>
                                    @endfor

                                    <td class="bold" style="font-size: 7px; line-height: 1.3; padding: 2px 1px;">
                                        @if ($logs->isNotEmpty())
                                            <div>{{ $bwDisplay }}</div>
                                            <div style="color: #cf1322; font-weight: bold; margin-top: 2px;">
                                                {{ $colorDisplay }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>{{ $perbaikanDisplay }}</td>
                                    <td>{{ $teknisi1Display }}</td>
                                    <td>{{ $teknisi2Display }}</td>
                                    <td class="bg-kontrak">{{ $dep->no_kontrak ?? ($m->no_kontrak ?? '-') }}</td>
                                    <td class="{{ $classKeterangan }}">{{ $textKeterangan }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <table class="stat-table">
                <thead>
                    <tr>
                        <th class="stat-rm">TOTAL RM</th>
                        <th class="stat-cm">TOTAL CM</th>
                        <th class="stat-rn">TOTAL TN</th>
                        <th class="stat-rr">TOTAL RR</th>
                        <th class="stat-blm">BELUM RM</th>
                        <th class="stat-sudah">SUDAH RM</th>
                        <th class="stat-total">TOTAL MESIN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="stat-rm">{{ $stat['RM'] }}</td>
                        <td class="stat-cm">{{ $stat['CM'] }}</td>
                        <td class="stat-rn">{{ $stat['TN'] }}</td>
                        <td class="stat-rr">{{ $stat['RR'] }}</td>
                        <td class="stat-blm">{{ $stat['BELUM_RM'] }}</td>
                        <td class="stat-sudah">{{ $stat['SUDAH_RM'] }}</td>
                        <td class="stat-total">{{ $stat['TOTAL_MESIN'] }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    @endforeach
</body>

</html>
