<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Status MRC - <?php echo e($bulan); ?> <?php echo e($tahun); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 12px;
        }

        .summary {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .summary-box {
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .box-total {
            background: #e0e7ff;
            color: #3730a3;
        }

        .box-sudah {
            background: #d1fae5;
            color: #065f46;
        }

        .box-belum {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            background: #e2e8f0;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 10px;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
        }

        .status-sudah {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
        }

        .status-belum {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .ttd {
            text-align: center;
            width: 180px;
        }

        .ttd .line {
            margin-top: 55px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 210mm;
            margin: 10px auto;
            padding: 10px 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Rekap Status MRC — <?php echo e($bulan); ?> <?php echo e($tahun); ?></h2>
            <p>Periode &nbsp;&nbsp;: <?php echo e($bulan); ?> <?php echo e($tahun); ?></p>
            <p>Dicetak &nbsp;&nbsp;: <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB</p>
        </div>
        <hr>

        <?php
            $totalMesin = $machines->count();
            $sudah = $machines->filter(fn($m) => isset($mrcLogs[$m->id]))->count();
            $belum = $totalMesin - $sudah;
        ?>

        <div class="summary">
            <div class="summary-box box-total">Total Mesin: <?php echo e($totalMesin); ?></div>
            <div class="summary-box box-sudah">✅ Sudah MRC: <?php echo e($sudah); ?></div>
            <div class="summary-box box-belum">❌ Belum MRC: <?php echo e($belum); ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Customer</th>
                    <th style="width:95px;">SN Mesin</th>
                    <th style="width:80px;">Model</th>
                    <th style="width:75px;">Teknisi</th>
                    <th class="center" style="width:65px;">Status</th>
                    <th class="center" style="width:70px;">Ctr BW</th>
                    <th class="center" style="width:60px;">Usage BW</th>
                    <th class="center" style="width:70px;">Ctr CL</th>
                    <th class="center" style="width:60px;">Usage CL</th>
                    <th class="center" style="width:65px;">Tgl MRC</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $log = $mrcLogs[$machine->id] ?? null;
                        $sudahMrc = $log !== null;
                    ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td><?php echo e($machine->customer?->nama_customer ?? '-'); ?></td>
                        <td><?php echo e($machine->serial_number); ?></td>
                        <td><?php echo e($machine->tipe_model); ?></td>
                        <td><?php echo e($machine->customer?->technician?->nama_technician ?? '-'); ?></td>
                        <td class="center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahMrc): ?>
                                <span class="status-sudah">✅ Sudah</span>
                            <?php else: ?>
                                <span class="status-belum">❌ Belum</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="center"><?php echo e($sudahMrc ? number_format($log->counter_bw) : '-'); ?></td>
                        <td class="center"><?php echo e($sudahMrc ? number_format($log->usage_bw) : '-'); ?></td>
                        <td class="center"><?php echo e($sudahMrc ? number_format($log->counter_color) : '-'); ?></td>
                        <td class="center"><?php echo e($sudahMrc ? number_format($log->usage_color) : '-'); ?></td>
                        <td class="center">
                            <?php echo e($sudahMrc ? \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') : '-'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Total</td>
                    <td class="center"><?php echo e($totalMesin); ?> mesin</td>
                    <td class="center">
                        <?php echo e(number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->counter_bw))); ?>

                    </td>
                    <td class="center">
                        <?php echo e(number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->usage_bw))); ?>

                    </td>
                    <td class="center">
                        <?php echo e(number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->counter_color))); ?>

                    </td>
                    <td class="center">
                        <?php echo e(number_format($machines->filter(fn($m) => isset($mrcLogs[$m->id]))->sum(fn($m) => $mrcLogs[$m->id]->usage_color))); ?>

                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Kepala Cabang</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
            <div class="ttd">
                <p>Bandung, <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?></p>
                <p>Dibuat oleh,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>
    </div>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/reports/mrc-rekap.blade.php ENDPATH**/ ?>