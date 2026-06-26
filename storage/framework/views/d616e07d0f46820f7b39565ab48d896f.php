<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Rayon</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
            color: #000;
        }

        .main-title {
            background: #005088;
            color: #fff;
            text-align: center;
            padding: 6px;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sub-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            padding: 3px;
            border: 1px solid #005088;
            border-top: none;
        }

        .periode {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            padding: 3px;
            border: 1px solid #005088;
            border-top: none;
            margin-bottom: 16px;
        }

        .rayon-block {
            margin-bottom: 20px;
        }

        .rayon-wrapper {
            display: flex;
            width: 100%;
            border: 1px solid #333;
        }

        /* Tabel kiri: data per kota + jumlah + persen */
        .tbl-left {
            flex: 1;
            border-collapse: collapse;
        }

        .tbl-left th,
        .tbl-left td {
            border: 1px solid #333;
            text-align: center;
            padding: 4px 3px;
        }

        /* Panel kanan: rata-rata + keterangan */
        .panel-right {
            display: flex;
            flex-direction: column;
            border-left: 1px solid #333;
            min-width: 320px;
        }

        .panel-right-header {
            display: flex;
            border-bottom: 1px solid #333;
        }

        .panel-right-header .ph {
            background: #92d050;
            font-weight: bold;
            text-align: center;
            padding: 4px 3px;
            flex: 1;
            border-right: 1px solid #333;
            font-size: 11px;
            line-height: 1.4;
        }

        .panel-right-header .ph:last-child {
            border-right: none;
        }

        .panel-right-body {
            display: flex;
            flex: 1;
        }

        .panel-rata {
            flex: 1;
            text-align: center;
            vertical-align: middle;
            padding: 6px;
            font-weight: bold;
            font-size: 13px;
            border-right: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .panel-ket {
            flex: 1.5;
            padding: 6px;
            font-size: 10px;
            vertical-align: top;
            text-align: center;
        }

        .th-group {
            background: #92d050;
            font-weight: bold;
            font-size: 11px;
        }

        .th-tipe {
            background: #ffff00;
            font-weight: bold;
        }

        .td-kota {
            text-align: left;
            padding-left: 6px !important;
        }

        .tr-jumlah td {
            background: #92d050;
            font-weight: bold;
        }

        .tr-persen td {
            background: #ffff00;
        }

        @media print {
            body {
                padding: 10px;
            }

            .rayon-block {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="main-title">LAPORAN BULANAN KINERJA TEKNIK</div>
    <div class="sub-title">PT. DINAMIKA GLOBAL GEMILANG - CIREBON</div>
    <div class="periode">
        PERIODE : <?php echo e(strtoupper(\Carbon\Carbon::create($year, $month)->translatedFormat('F Y'))); ?>

    </div>

    <?php $groupNum = 1; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaRayon => $techs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $allLogs = $techs->flatMap(fn($t) => $t->serviceLogs);

            // Ambil kota dari relasi customer
            $kotaList = $allLogs->map(fn($log) => $log->customer->kota ?? null)->unique()->filter()->sort()->values();

            $jumlahTeknisi = $techs->count();
            $totalKunjungan = $allLogs->count();
            $rataRata =
                $hariKerja > 0 && $jumlahTeknisi > 0 ? round($totalKunjungan / $hariKerja / $jumlahTeknisi, 1) : 0;
            $namaTeknis = $techs->pluck('nama_technician')->implode(', ');

            // Data per kota per tipe (CM, RM, RN, RR -- dari tipe_kunjungan)
            // "Mesin" dan "RM Tertunda" TIDAK dihitung dari tipe_kunjungan -- lihat di bawah.
            $dataTable = [];
            foreach ($kotaList as $kota) {
                $logsKota = $allLogs->filter(fn($log) => ($log->customer->kota ?? null) === $kota);
                foreach ($tipeKolom as $tipe) {
                    if ($tipe === 'Mesin' || $tipe === 'RM Tertunda') {
                        continue;
                    }
                    $dataTable[$kota][$tipe] = $logsKota->where('tipe_kunjungan', $tipe)->count();
                }
                $keyRayonKota = $namaRayon . '||' . $kota;
                // Total Mesin = jumlah unit Rented di kota ini, khusus rayon ini
                $dataTable[$kota]['Mesin'] = $jumlahMesinPerRayonKota[$keyRayonKota] ?? 0;
                // RM Tertunda = dari mesin di atas, yang belum pernah dikunjungi RM bulan ini
                $dataTable[$kota]['RM Tertunda'] = $rmTertundaPerRayonKota[$keyRayonKota] ?? 0;
            }

            // Total & persentase per tipe kunjungan
            $totalPerTipe = [];
            foreach ($tipeKolom as $tipe) {
                if ($tipe === 'Mesin') {
                    $totalPerTipe[$tipe] = $kotaList->sum(
                        fn($kota) => $jumlahMesinPerRayonKota[$namaRayon . '||' . $kota] ?? 0,
                    );
                } elseif ($tipe === 'RM Tertunda') {
                    $totalPerTipe[$tipe] = $kotaList->sum(
                        fn($kota) => $rmTertundaPerRayonKota[$namaRayon . '||' . $kota] ?? 0,
                    );
                } else {
                    $totalPerTipe[$tipe] = $allLogs->where('tipe_kunjungan', $tipe)->count();
                }
            }

            // Grand total untuk persentase tetap dihitung dari kunjungan saja (tanpa Mesin
            // dan RM Tertunda, karena keduanya bukan "kunjungan" melainkan status unit).
            $grandTotalKunjungan = array_sum(
                array_filter(
                    $totalPerTipe,
                    fn($tipe) => $tipe !== 'Mesin' && $tipe !== 'RM Tertunda',
                    ARRAY_FILTER_USE_KEY,
                ),
            );

            $persenPerTipe = [];
            foreach ($tipeKolom as $tipe) {
                if ($tipe === 'Mesin' || $tipe === 'RM Tertunda') {
                    $persenPerTipe[$tipe] = '-'; // tidak relevan dipersenkan
                } else {
                    $persenPerTipe[$tipe] =
                        $grandTotalKunjungan > 0
                            ? round(($totalPerTipe[$tipe] / $grandTotalKunjungan) * 100) . '%'
                            : '0%';
                }
            }
        ?>

        <div class="rayon-block">
            <div class="rayon-wrapper">

                
                <table class="tbl-left">
                    <thead>
                        <tr>
                            <th class="th-group" style="width:120px">
                                GROUP-<?php echo e($groupNum); ?><br><?php echo e(strtoupper($namaRayon)); ?>

                            </th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tipeKolom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="th-group">TOTAL<br><?php echo e(strtoupper($tipe)); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kotaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-kota"><?php echo e(strtoupper($kota)); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tipeKolom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td><?php echo e($dataTable[$kota][$tipe] ?? 0); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="td-kota" colspan="<?php echo e(count($tipeKolom) + 1); ?>">-</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <tr class="tr-jumlah">
                            <td class="td-kota">JUMLAH</td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tipeKolom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td><?php echo e($totalPerTipe[$tipe]); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>

                        
                        <tr class="tr-persen">
                            <td class="td-kota">PERSENTASE</td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tipeKolom; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td><?php echo e($persenPerTipe[$tipe]); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </tbody>
                </table>

                
                <div class="panel-right">
                    <div class="panel-right-header">
                        <div class="panel-rata">RATA - RATA<br>KUNJUNGAN / HARI</div>
                        <div class="panel-ket">KETERANGAN</div>
                    </div>
                    <div class="panel-right-body">
                        <div class="panel-rata"><?php echo e($rataRata); ?></div>
                        <div class="panel-ket">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <br>

        <?php $groupNum++; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/print/performance-rayon.blade.php ENDPATH**/ ?>