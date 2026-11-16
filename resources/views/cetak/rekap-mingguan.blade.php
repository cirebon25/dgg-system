<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Teknisi Mingguan - DGG System</title>
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
            background: #09bd15;
            color: #fff;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            text-transform: uppercase;
            position: relative;
            display: flex;
            align-items: center;
            min-height: 16px;
        }

        .nama-rayon-kiri {
            position: relative;
            z-index: 2;
        }

        .rayon-title .ket-singkatan {
            margin: 0;
            color: #131111;
            font-size: 7px;
            font-weight: normal;
            letter-spacing: 0.3px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            text-align: center;
            z-index: 1;
        }

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
            font-weight: bold;
            color: #000;
        }

        .stat-table th {
            font-size: 7.5px;
            text-transform: uppercase;
        }

        .stat-table td {
            font-size: 8.5px;
        }

        .stat-rn {
            background-color: #09f043 !important;
        }

        .stat-rm {
            background-color: #072cfa !important;
            color: #ffffff !important;
        }

        .stat-cm {
            background-color: #f71a06 !important;
            color: #ffffff !important;
        }

        .stat-tn {
            background-color: #f5f5f5 !important;
            color: #595959 !important;
        }

        .stat-rr {
            background-color: #c23f1f !important;
            color: #f8f3f0 !important;
        }

        .stat-blm {
            background-color: #fff1f0 !important;
            color: #cf1322 !important;
        }

        .stat-sudah {
            background-color: #e6f7ff !important;
            color: #0050b3 !important;
        }

        .stat-total {
            background-color: #fffb8f !important;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main-table th {
            background: #09bd15;
            color: rgb(44, 42, 42);
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

        .row-kota {
            background-color: #eceff1 !important;
            font-weight: bold;
            padding: 5px;
            font-size: 8.5px;
            color: #1a237e;
            text-align: left !important;
        }

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
            color: #0f0f0f !important;
            font-weight: bold;
        }

        .bg-rr {
            background-color: #469256 !important;
            color: #e6e2e0 !important;
            font-weight: bold;
        }

        .bg-jk {
            background-color: #97d1e7 !important;
            color: #614700 !important;
            font-weight: bold;
        }

        .bg-l {
            background-color: #e481e4 !important;
            color: #121312 !important;
            font-weight: bold;
        }

        .bg-tn {
            background-color: #c46060 !important;
            color: #faf3f3 !important;
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

        .col-sabtu {
            background-color: #b3937e !important;
        }

        .col-minggu {
            background-color: #b3937e !important;
        }

        /* Baris mesin lama (histori penggantian) */
        .row-historis {
            background-color: #fafafa !important;
            font-style: italic;
            opacity: 0.85;
        }

        .row-historis td {
            border-top: 1px dashed #999 !important;
        }

        .ket-mesin-lama {
            font-size: 6px;
            font-weight: normal;
            color: #ad6800;
            display: block;
            margin-top: 1px;
        }

        .bg-ket-historis {
            background-color: #fff7e6 !important;
            color: #ad6800 !important;
            font-weight: bold;
            text-transform: uppercase;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
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
        <h2 style="margin:3px 0; font-size: 12px;">LAPORAN MONITORING UNIT & HISTORI SERVIS MINGGUAN</h2>
        <p style="margin:0; font-size: 10px;">Periode:
            {{ $start->translatedFormat('d F Y') }} s/d {{ $end->translatedFormat('d F Y') }}</p>
    </header>

    @php
        // Rentang waktu penuh (awal hari Senin s/d akhir hari Minggu)
        $mulai = $start->copy()->startOfDay();
        $akhir = $end->copy()->endOfDay();
    @endphp

    {{-- =============================================
         LAPORAN PER RAYON (1 MINGGU)
    ============================================= --}}
    @foreach ($rayons as $rayon)
        @php
            // 1. Deployment aktif (mesin yang saat ini terpasang)
            $deploymentsAktif = \App\Models\Deployment::with([
                'customer',
                'machine.serviceLogs' => function ($query) use ($mulai, $akhir) {
                    $query->whereBetween('tanggal', [$mulai, $akhir])->orderBy('tanggal', 'asc');
                },
            ])
                ->whereHas('customer', function ($q) use ($rayon) {
                    $q->where('rayon_id', $rayon->id);
                })
                ->get();

            // 2. Riwayat pergantian mesin (RR) yang terjadi di minggu ini
            $replacementsMingguIni = \App\Models\MachineReplacement::with([
                'customer',
                'newMachine',
                'oldMachine.serviceLogs' => function ($query) use ($mulai, $akhir) {
                    $query->whereBetween('tanggal', [$mulai, $akhir])->orderBy('tanggal', 'asc');
                },
            ])
                ->whereHas('customer', function ($q) use ($rayon) {
                    $q->where('rayon_id', $rayon->id);
                })
                ->whereBetween('tanggal', [$mulai, $akhir])
                ->get();

            $deploymentsHistoris = $replacementsMingguIni->map(function ($rep) {
                $depLama = \App\Models\Deployment::withTrashed()
                    ->where('customer_id', $rep->customer_id)
                    ->where('machine_id', $rep->old_machine_id)
                    ->orderByDesc('deleted_at')
                    ->first();

                return (object) [
                    'id' => 'hist-' . $rep->id,
                    'customer_id' => $rep->customer_id,
                    'customer' => $rep->customer,
                    'machine' => $rep->oldMachine,
                    'no_kontrak' => $depLama->no_kontrak ?? null,
                    'tanggal_instal' => $depLama->tanggal_instal ?? null,
                    'is_historis' => true,
                    'tanggal_ganti' => $rep->tanggal,
                    'sn_baru' => $rep->newMachine?->serial_number,
                ];
            });

            // Gabungkan aktif + historis; baris mesin lama tepat di bawah mesin barunya
            $deployments = $deploymentsAktif
                ->concat($deploymentsHistoris)
                ->sortBy(function ($d) {
                    return sprintf('%08d-%d', $d->customer_id, $d->is_historis ?? false ? 1 : 0);
                })
                ->values();

            // STATISTIK
            $allLogsAktif = $deploymentsAktif->flatMap(
                fn($dep) => $dep->machine ? $dep->machine->serviceLogs : collect(),
            );
            $allLogsHistoris = $deploymentsHistoris->flatMap(
                fn($dep) => $dep->machine ? $dep->machine->serviceLogs : collect(),
            );
            $allLogs = $allLogsAktif->concat($allLogsHistoris);

            $sudahRmCount = 0;
            $belumRmCount = 0;
            foreach ($deploymentsAktif as $dep) {
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
                'RN' => $allLogs->where('tipe_kunjungan', 'RN')->count(),
                'RR' => $allLogs->where('tipe_kunjungan', 'RR')->count(),
                'SUDAH_RM' => $sudahRmCount,
                'BELUM_RM' => $belumRmCount,
                'TOTAL_MESIN' => $deploymentsAktif->whereNotNull('machine_id')->count(),
            ];

            $deploymentsByKota = $deployments->groupBy(function ($dep) {
                return $dep->customer && $dep->customer->kota ? strtoupper($dep->customer->kota) : 'TANPA KOTA';
            });
        @endphp

        <div class="page-break">
            <div class="rayon-title">
                <span class="nama-rayon-kiri"> RAYON: {{ $rayon->nama_rayon }}</span>
                <p class="ket-singkatan">
                    KETERANGAN : RM = REGULER MAINTENANCE | CM = CALL MAINTENANCE | TN = CALL TONER | RN = INSTALL
                    MACHINE | JK = JARINGAN KOMPUTER | L = LANJUTAN | RR = GANTI MESIN
                </p>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th rowspan="2" width="18">NO</th>
                        <th rowspan="2" width="170">NAMA CUSTOMER</th>
                        <th rowspan="2" width="55">TIPE MESIN</th>
                        <th rowspan="2" width="70">NO SERI</th>
                        <th rowspan="2" width="50">TGL PASANG</th>
                        <th colspan="7">TANGGAL KUNJUNGAN</th>
                        <th rowspan="2" width="90">COUNTER AKHIR</th>
                        <th rowspan="2" width="130">PERBAIKAN</th>
                        <th colspan="2" width="120">TEKNISI</th>
                        <th rowspan="2" width="60">NO KONTRAK</th>
                        <th rowspan="2" width="55">KETERANGAN</th>
                    </tr>
                    <tr>
                        @foreach ($days as $d)
                            @php
                                $thWeekendClass =
                                    $d->dayOfWeek == 6 ? 'col-sabtu' : ($d->dayOfWeek == 0 ? 'col-minggu' : '');
                            @endphp
                            <th width="32" class="{{ $thWeekendClass }}"
                                style="font-size: 7px; padding: 1px; white-space: nowrap;">
                                {{ $d->translatedFormat('D') }}<br>{{ $d->format('d/m') }}
                            </th>
                        @endforeach
                        <th width="60">TEKNISI 1</th>
                        <th width="60">TEKNISI 2</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp

                    @foreach ($deploymentsByKota as $kota => $itemsInKota)
                        <tr>
                            <td colspan="18" class="row-kota">
                                KOTA / KABUPATEN: {{ $kota }}
                            </td>
                        </tr>

                        @foreach ($itemsInKota as $dep)
                            @if ($dep->customer && $dep->machine)
                                @php
                                    $isHistoris = $dep->is_historis ?? false;
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
                                            ->map(function ($log) {
                                                if (strtoupper($log->tipe_kunjungan) === 'RN') {
                                                    return 'INSTALASI UNIT BARU';
                                                }
                                                return $log->perbaikan
                                                    ? \Illuminate\Support\Str::limit($log->perbaikan, 35)
                                                    : '-';
                                            })
                                            ->implode(' | ');
                                    }

                                    $hasRM = $logs->contains(fn($log) => strtoupper($log->tipe_kunjungan) === 'RM');
                                    $textKeterangan = $hasRM ? 'sudah rm' : 'blm rm';
                                    $classKeterangan = $hasRM ? 'bg-ket-sudah' : 'bg-ket-blm';

                                    // Override keterangan untuk baris histori mesin yang sudah diganti
                                    if ($isHistoris) {
                                        $tglGantiFmt = $dep->tanggal_ganti
                                            ? \Carbon\Carbon::parse($dep->tanggal_ganti)->format('d/m/Y')
                                            : '-';
                                        $snBaruDisplay = $dep->sn_baru ?? '-';
                                        $textKeterangan = 'diganti ' . $tglGantiFmt . ' → SN ' . $snBaruDisplay;
                                        $classKeterangan = 'bg-ket-historis';
                                    }
                                @endphp
                                <tr class="{{ $isHistoris ? 'row-historis' : '' }}">
                                    <td><b>{{ $isHistoris ? '↳' : $no++ }}</b></td>
                                    <td class="text-left bold">
                                        {{ $customer->nama_customer }}
                                        <div
                                            style="font-weight: normal; font-size: 6.5px; color: #555; margin-top: 2px;">
                                            {{ $customer->alamat ?? '-' }}
                                        </div>
                                        @if ($isHistoris)
                                            <span class="ket-mesin-lama">GANTI MESIN
                                                {{ \Carbon\Carbon::parse($dep->tanggal_ganti)->format('d M Y') }}
                                                dengan SN {{ $dep->sn_baru ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $m->tipe_model }}</td>
                                    <td class="bold bg-seri">{{ $m->serial_number }}</td>
                                    <td>
                                        {{ $dep->tanggal_instal ? \Carbon\Carbon::parse($dep->tanggal_instal)->format('d M Y') : '-' }}
                                    </td>

                                    {{-- 7 kolom hari (Senin - Minggu) --}}
                                    @foreach ($days as $d)
                                        @php
                                            $logsHariIni = $logs->filter(
                                                fn($log) => \Carbon\Carbon::parse($log->tanggal)->isSameDay($d),
                                            );
                                            $logHariIni = $logsHariIni->first();

                                            $bgClass =
                                                $d->dayOfWeek == 6
                                                    ? 'col-sabtu'
                                                    : ($d->dayOfWeek == 0
                                                        ? 'col-minggu'
                                                        : '');
                                            if ($logHariIni) {
                                                $bgClass = match (strtoupper($logHariIni->tipe_kunjungan)) {
                                                    'RM' => 'bg-rm',
                                                    'CM' => 'bg-cm',
                                                    'RN' => 'bg-rn',
                                                    'RR' => 'bg-rr',
                                                    'JK' => 'bg-jk',
                                                    'L' => 'bg-l',
                                                    'TN' => 'bg-tn',
                                                    default => $bgClass,
                                                };
                                            }
                                        @endphp
                                        <td class="{{ $bgClass }}"
                                            style="font-size: 7px; font-weight: bold; white-space: nowrap; padding: 2px 0;">
                                            {{ $logsHariIni->pluck('tipe_kunjungan')->implode('/') }}
                                        </td>
                                    @endforeach

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
                        <th class="stat-rn">TOTAL RN</th>
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
                        <td class="stat-rn">{{ $stat['RN'] }}</td>
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
