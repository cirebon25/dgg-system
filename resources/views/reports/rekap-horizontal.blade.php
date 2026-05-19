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

        .bg-kontrak {
            background-color: #f6ffed !important;
            color: #1b5e20 !important;
            font-weight: bold;
        }

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

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* =============================================
           TABEL PRESTASI - HALAMAN TERAKHIR
        ============================================= */
        .prestasi-page {
            padding: 10px;
        }

        .prestasi-page-title {
            background: #7b6000;
            color: #fff200;
            font-weight: bold;
            font-size: 13px;
            padding: 8px 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        table.prestasi-table {
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
            width: auto;
        }

        .prestasi-table th,
        .prestasi-table td {
            border: 1px solid #555;
            padding: 5px 10px;
            text-align: center;
            white-space: nowrap;
        }

        .pres-header-group {
            background-color: #fff200 !important;
            color: #000 !important;
            font-weight: bold;
            text-align: left;
            min-width: 110px;
        }

        .pres-header-col {
            background-color: #fff200 !important;
            color: #000 !important;
            font-weight: bold;
            min-width: 70px;
        }

        .pres-header-rata {
            background-color: #fff200 !important;
            color: #000 !important;
            font-weight: bold;
            font-size: 8px;
            min-width: 130px;
        }

        .pres-row-data td {
            background-color: #ffffff;
            color: #222;
        }

        .pres-row-jumlah td {
            background-color: #fff200 !important;
            color: #000 !important;
            font-weight: bold;
        }

        .pres-row-persen td {
            background-color: #ffffff;
            color: #222;
            font-style: italic;
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

    {{-- =============================================
         BAGIAN 1: LAPORAN PER RAYON (TIDAK DIUBAH)
    ============================================= --}}
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

            $deploymentsByKota = $deployments->groupBy(function ($dep) {
                return $dep->customer->kota ? strtoupper($dep->customer->kota) : 'TANPA KOTA';
            });
        @endphp

        {{-- Setiap rayon selalu page-break (termasuk yang terakhir, karena halaman prestasi ada di bawah) --}}
        <div class="page-break">

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
                                    <td>{{ $dep->tgl_pasang ? \Carbon\Carbon::parse($dep->tgl_pasang)->format('d/m/y') : ($m->tgl_pasang ? \Carbon\Carbon::parse($m->tgl_pasang)->format('d/m/y') : '-') }}</td>

                                    @for ($day = 1; $day <= 31; $day++)
                                        @php
                                            $logHariIni = $logs->first(
                                                fn($log) => \Carbon\Carbon::parse($log->tanggal)->day == $day,
                                            );
                                            $bgClass = '';
                                            if ($logHariIni) {
                                                switch (strtoupper($logHariIni->tipe_kunjungan)) {
                                                    case 'RM': $bgClass = 'bg-rm'; break;
                                                    case 'CM': $bgClass = 'bg-cm'; break;
                                                    case 'RN': $bgClass = 'bg-rn'; break;
                                                    case 'RR': $bgClass = 'bg-rr'; break;
                                                    case 'JK': $bgClass = 'bg-jk'; break;
                                                    case 'L':  $bgClass = 'bg-l';  break;
                                                    case 'TN': $bgClass = 'bg-tn'; break;
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

    {{-- =============================================
         BAGIAN 2: HALAMAN PRESTASI (LEMBAR TERPISAH PALING BAWAH)
         Dikelompokkan per Rayon → per Kota (mengikuti kolom kota di tabel customers)
    ============================================= --}}
    <div class="prestasi-page">

        <div class="prestasi-page-title">📊 REKAP PRESTASI SEMUA RAYON — PERIODE:
            {{ Carbon\Carbon::create()->year($year)->month($month)->translatedFormat('F') }} {{ $year }}
        </div>

        @foreach ($rayons as $rayon)
            @php
                /*
                 * Ambil semua deployment untuk rayon ini beserta serviceLogs bulan ini
                 */
                $depsPrestasi = \App\Models\Deployment::with([
                    'customer',
                    'machine.serviceLogs' => function ($query) use ($month, $year) {
                        $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->orderBy('tanggal', 'asc');
                    },
                ])
                    ->whereHas('customer', function ($q) use ($rayon) {
                        $q->where('rayon_id', $rayon->id);
                    })
                    ->get();

                /*
                 * Kelompokkan per KOTA (mengikuti kolom kota di tabel customers,
                 * sama persis dengan pengelompokan di tabel utama atas)
                 */
                $depsByKotaPrestasi = $depsPrestasi->groupBy(function ($dep) {
                    return $dep->customer->kota ? strtoupper($dep->customer->kota) : 'TANPA KOTA';
                });

                /*
                 * Hitung statistik per kota
                 */
                $kotaStats = [];
                foreach ($depsByKotaPrestasi as $kotaNama => $kotaDeps) {
                    $kotaLogs = $kotaDeps->flatMap(function ($dep) {
                        return $dep->machine ? $dep->machine->serviceLogs : collect();
                    });

                    $rmTertunda = $kotaDeps->filter(function ($dep) {
                        if (!$dep->machine) return false;
                        return !$dep->machine->serviceLogs->contains(
                            fn($log) => strtoupper($log->tipe_kunjungan) === 'RM'
                        );
                    })->count();

                    $kotaStats[$kotaNama] = [
                        'CM'          => $kotaLogs->where('tipe_kunjungan', 'CM')->count(),
                        'RM'          => $kotaLogs->where('tipe_kunjungan', 'RM')->count(),
                        'RN'          => $kotaLogs->where('tipe_kunjungan', 'RN')->count(),
                        'RR'          => $kotaLogs->where('tipe_kunjungan', 'RR')->count(),
                        'TOTAL_MESIN' => $kotaDeps->whereNotNull('machine_id')->count(),
                        'RM_TERTUNDA' => $rmTertunda,
                    ];
                }

                /*
                 * Baris JUMLAH (total semua kota dalam rayon ini)
                 */
                $jumlahPrestasi = [
                    'CM'          => array_sum(array_column($kotaStats, 'CM')),
                    'RM'          => array_sum(array_column($kotaStats, 'RM')),
                    'RN'          => array_sum(array_column($kotaStats, 'RN')),
                    'RR'          => array_sum(array_column($kotaStats, 'RR')),
                    'TOTAL_MESIN' => array_sum(array_column($kotaStats, 'TOTAL_MESIN')),
                    'RM_TERTUNDA' => array_sum(array_column($kotaStats, 'RM_TERTUNDA')),
                ];

                /*
                 * Baris PERSENTASE
                 */
                $totalMesinPres = $jumlahPrestasi['TOTAL_MESIN'] ?: 1;
                $persenPrestasi = [
                    'CM'          => round(($jumlahPrestasi['CM'] / $totalMesinPres) * 100, 2) . '%',
                    'RM'          => round(($jumlahPrestasi['RM'] / $totalMesinPres) * 100, 2) . '%',
                    'RN'          => '',
                    'RR'          => '',
                    'TOTAL_MESIN' => '',
                    'RM_TERTUNDA' => round(($jumlahPrestasi['RM_TERTUNDA'] / $totalMesinPres) * 100, 2) . '%',
                ];

                /*
                 * Rata-rata kunjungan per hari
                 */
                $jumlahHari   = \Carbon\Carbon::create($year, $month)->daysInMonth;
                $totalKunjungan = $jumlahPrestasi['CM'] + $jumlahPrestasi['RM']
                                + $jumlahPrestasi['RN'] + $jumlahPrestasi['RR'];
                $rataHari = $jumlahHari > 0 ? round($totalKunjungan / $jumlahHari, 1) : 0;
            @endphp

            {{-- Label Rayon --}}
            <div style="margin-top: 14px; margin-bottom: 4px;">
                <span style="background:#000; color:#fff; font-weight:bold; font-size:9px;
                             padding: 3px 10px; text-transform:uppercase; letter-spacing:1px;">
                    📍 RAYON: {{ $rayon->nama_rayon }}
                </span>
            </div>

            <table class="prestasi-table">
                <thead>
                    <tr>
                        <th class="pres-header-group">KOTA / KABUPATEN</th>
                        <th class="pres-header-col">TOTAL<br>CM</th>
                        <th class="pres-header-col">TOTAL<br>RM</th>
                        <th class="pres-header-col">TOTAL<br>RN</th>
                        <th class="pres-header-col">TOTAL<br>RR</th>
                        <th class="pres-header-col">TOTAL<br>MESIN</th>
                        <th class="pres-header-col">RM<br>TERTUNDA</th>
                        <th class="pres-header-rata" rowspan="{{ count($kotaStats) + 3 }}"
                            style="vertical-align:middle; text-align:center; padding: 8px 16px;">
                            RATA - RATA<br>KUNJUNGAN / HARI<br>
                            <span style="font-size: 18px; font-weight: bold; color:#000; display:block; margin-top:6px;">
                                {{ $rataHari }}
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Baris per Kota --}}
                    @foreach ($kotaStats as $kotaNama => $kotaStat)
                        <tr class="pres-row-data">
                            <td style="text-align:left; font-weight:bold;">{{ $kotaNama }}</td>
                            <td>{{ $kotaStat['CM'] }}</td>
                            <td>{{ $kotaStat['RM'] }}</td>
                            <td>{{ $kotaStat['RN'] }}</td>
                            <td>{{ $kotaStat['RR'] }}</td>
                            <td>{{ $kotaStat['TOTAL_MESIN'] }}</td>
                            <td>{{ $kotaStat['RM_TERTUNDA'] }}</td>
                        </tr>
                    @endforeach

                    {{-- Baris JUMLAH --}}
                    <tr class="pres-row-jumlah">
                        <td style="text-align:left;">JUMLAH</td>
                        <td>{{ $jumlahPrestasi['CM'] }}</td>
                        <td>{{ $jumlahPrestasi['RM'] }}</td>
                        <td>{{ $jumlahPrestasi['RN'] }}</td>
                        <td>{{ $jumlahPrestasi['RR'] }}</td>
                        <td>{{ $jumlahPrestasi['TOTAL_MESIN'] }}</td>
                        <td>{{ $jumlahPrestasi['RM_TERTUNDA'] }}</td>
                    </tr>

                    {{-- Baris PERSENTASE --}}
                    <tr class="pres-row-persen">
                        <td style="text-align:left; font-weight:bold;">PERSENTASE</td>
                        <td>{{ $persenPrestasi['CM'] }}</td>
                        <td>{{ $persenPrestasi['RM'] }}</td>
                        <td>{{ $persenPrestasi['RN'] }}</td>
                        <td>{{ $persenPrestasi['RR'] }}</td>
                        <td>{{ $persenPrestasi['TOTAL_MESIN'] }}</td>
                        <td>{{ $persenPrestasi['RM_TERTUNDA'] }}</td>
                    </tr>
                </tbody>
            </table>

        @endforeach

    </div>
    {{-- AKHIR HALAMAN PRESTASI --}}

</body>

</html>