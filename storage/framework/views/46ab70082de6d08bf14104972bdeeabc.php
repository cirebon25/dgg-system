<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional DGG - <?php echo e($namaBulan); ?> <?php echo e($year); ?></title>
    <style>
        /* 1. RESET & PRINT SETTINGS */
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-print-color-adjust: exact; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 9px; 
            padding: 10px 15px; 
            color: #333; 
            line-height: 1.1;
        }

        /* 2. HEADER UTAMA */
        .header { 
            width: 100%; text-align: center; border-bottom: 2px double #000; 
            padding-bottom: 2px !important; margin-bottom: 5px !important; 
        }
        .header h2 { font-size: 16px; text-transform: uppercase; line-height: 1.0; }
        .header p { font-size: 10px; font-weight: bold; line-height: 1.0; }

        /* TANGGAL CETAK PER HALAMAN */
        .print-date-page {
            text-align: right; 
            font-size: 8px; 
            font-style: italic; 
            margin-bottom: 2px;
        }

        /* 3. RAYON & KOTA HEADER */
        .rayon-box { page-break-before: always; }
        .rayon-box:first-of-type { page-break-before: auto; }

        .rayon-header { 
            background-color: #1e293b; color: white; padding: 5px 10px !important; 
            font-weight: bold; font-size: 11px; margin-top: 2px !important;
            display: flex; justify-content: space-between; align-items: center; 
            text-transform: uppercase; 
        }
        
        .kota-subheader {
            background-color: #f1f5f9; color: #1e293b; padding: 3px 10px;
            font-weight: bold; font-size: 9px; border: 1px solid #cbd5e1;
            margin-top: 2px; text-transform: uppercase; border-left: 5px solid #1e293b;
        }

        /* 4. TABLE STYLING */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 5px !important; }
        th, td { border: 1px solid #000; padding: 3px 2px !important; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2 !important; font-size: 8px; text-transform: uppercase; }

        /* WARNA BARIS REVISIT */
        .row-revisit { background-color: #c9e782 !important; }

        /* 5. BADGE WARNA */
        .badge { padding: 2px 4px !important; border-radius: 3px; font-weight: bold; color: white !important; display: inline-block; font-size: 7px; }
        .bg-cm { background-color: #991b1b !important; }
        .bg-rn { background-color: #16a34a !important; }
        .bg-rm { background-color: #2563eb !important; }
        .bg-tn { background-color: #eab308 !important; color: #000 !important; }
        .bg-jk { background-color: #9333ea !important; }
        .bg-l  { background-color: #f97316 !important; }
        .bg-rr { background-color: #eb0b0b !important; color: #e9ec0c !important; }
        .bg-gray { background-color: #4b5563 !important; }

        /* SUMMARY FOOTER: JADI BESAR (GAJAH) */
        .summary-footer {
            margin-top: -3px; 
            padding: 10px 15px; 
            border: 2px solid #000;
            background-color: #f8fafc; 
            display: flex; 
            gap: 25px;
            justify-content: center;
            -webkit-print-color-adjust: exact;
        }
        .summary-footer b, .summary-footer span {
            font-size: 15px !important; /* UKURAN BESAR PESANAN BOSS */
        }

        @media print { 
            @page { size: landscape; margin: 0.5cm; } 
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>DINAMIKA GLOBAL GEMILANG (DGG)</h2>
        <p>LAPORAN PENGERJAAN UNIT & MAINTENANCE PER RAYON (<?php echo e($namaBulan); ?> <?php echo e($year); ?>)</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rayons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rayon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rayon-box">
            
            <div class="print-date-page;  font-size: 8px" >Lap. Service Tanggal Cetak: <?php echo e(date('d/m/Y H:i')); ?></div>

            <?php
                $rayonTotalUnit = 0;
                $rayonRM = 0;
                $rayonCM = 0;
                $rayonNotVisited = 0;
                $customersByCity = $rayon->customers->groupBy('kota'); 
            ?>

            <div class="rayon-header">
                <span>📍 RAYON: <?php echo e(strtoupper($rayon->nama_rayon)); ?></span>
                <span style="color: #facc15;">👨‍🔧 TIM: <?php echo e($rayon->technicians->pluck('nama_technician')->implode(', ')); ?></span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customersByCity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kota => $customers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="kota-subheader">🏙️ KOTA/KAB: <?php echo e(strtoupper($kota ?: 'Lainnya')); ?></div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 3%;">No</th>
                            <th style="width: 8%;">Tanggal</th>
                            <th style="width: 10%;">SN Mesin</th>
                            <th style="width: 18%;">Customer</th>
                            <th style="width: 4%;">Tipe</th>
                            <th style="width: 10%;">Counter</th>
                            <th style="width: 10%;">Usage</th>
                            <th style="width: 15%;">Sparepart</th>
                            <th style="width: 14%;">Perbaikan</th>
                            <th style="width: 8%;">Teknisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $rayonTotalUnit += $customer->deployments->count(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customer->deployments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $logs = \App\Models\ServiceLog::where('machine_id', $dep->machine_id)
                                        ->whereMonth('tanggal', $month)
                                        ->whereYear('tanggal', $year)
                                        ->with(['technician', 'serviceLogSpareparts.sparepart'])
                                        ->get();
                                    
                                    $rayonRM += $logs->where('tipe_kunjungan', 'RM')->count();
                                    $rayonCM += $logs->where('tipe_kunjungan', 'CM')->count();
                                ?>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logs->isEmpty()): ?>
                                    <?php $rayonNotVisited++; ?>
                                    <tr>
                                        <td><?php echo e($no++); ?></td>
                                        <td>-</td>
                                        <td><?php echo e($dep->machine->serial_number); ?></td>
                                        <td style="text-align:left;"><strong><?php echo e($customer->nama_customer); ?></strong></td>
                                        
                                        <td colspan="5" style="color: #e61607; font-weight: font-style: italic; font-size: 12px; background-color: #ececec !important;">
                                            BELUM DIKUNJUNGI 
                                        </td>
                                        <td>-</td>
                                    </tr>
                                <?php else: ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($logs->count() > 1 ? 'row-revisit' : ''); ?>">
                                        <td><?php echo e($no++); ?></td>
                                        <td><?php echo e($log->tanggal->format('d/m/y')); ?></td>
                                        <td><?php echo e($log->machine->serial_number); ?></td>
                                        <td style="text-align:left;"><strong><?php echo e($customer->nama_customer); ?></strong></td>
                                        <td>
                                            <span class="badge 
                                                <?php if($log->tipe_kunjungan == 'CM'): ?> bg-cm <?php elseif($log->tipe_kunjungan == 'RN'): ?> bg-rn
                                                <?php elseif($log->tipe_kunjungan == 'RM'): ?> bg-rm <?php elseif($log->tipe_kunjungan == 'TN'): ?> bg-tn
                                                <?php elseif($log->tipe_kunjungan == 'JK'): ?> bg-jk <?php elseif($log->tipe_kunjungan == 'L'): ?> bg-l
                                                <?php elseif($log->tipe_kunjungan == 'RR'): ?> bg-rr <?php else: ?> bg-gray <?php endif; ?>">
                                                <?php echo e($log->tipe_kunjungan); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: #e11d48; font-weight: bold;">C: <?php echo e(number_format($log->counter_color)); ?></span><br>
                                            <span style="color: #000; font-weight: bold;">B: <?php echo e(number_format($log->counter_bw)); ?></span>
                                        </td>
                                        <td>
                                            <span style="color: #e11d48; font-weight: bold;">C: <?php echo e(number_format($log->usage_color)); ?></span><br>
                                            <span style="color: #000; font-weight: bold;">B: <?php echo e(number_format($log->usage_bw)); ?></span>
                                        </td>
                                        <td style="text-align:left;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $log->serviceLogSpareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                                • <?php echo e($sp->sparepart->nama_sparepart); ?> <b>(<?php echo e($sp->jumlah); ?>)</b><br> 
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td style="text-align:left;"><?php echo e($log->perbaikan); ?></td>
                                        <td>
                                            <b><?php echo e($log->technician->nama_technician ?? '-'); ?></b>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->nama_teknisi_2): ?><br><span style="font-size: 7px; color: #555;">& <?php echo e($log->nama_teknisi_2); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="summary-footer">
                <b>📊 RINGKASAN RAYON <?php echo e(strtoupper($rayon->nama_rayon)); ?>:</b>
                <span>📟 POPULASI: <b><?php echo e($rayonTotalUnit); ?> UNIT</b></span>
                <span style="color: #2563eb;">🛠️ TOTAL RM: <b><?php echo e($rayonRM); ?></b></span>
                <span style="color: #991b1b;">⚠️ TOTAL CM: <b><?php echo e($rayonCM); ?></b></span>
                <span style="color: #e61607;">❌ BELUM DIKUNJUNGI: <b><?php echo e($rayonNotVisited); ?> UNIT</b></span>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html><?php /**PATH C:\laragon\www\dgg-system\resources\views/filament/pages/cetak-rekap-rayon.blade.php ENDPATH**/ ?>