<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tagihan MRC - <?php echo e($bulan ?? ''); ?> <?php echo e($tahun ?? ''); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 11px;
            color: #334155;
        }

        .container {
            width: 100%;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .title h2 {
            color: #2563eb;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background: #2563eb;
            color: #ffffff;
            padding: 8px 4px;
            font-size: 10px;
            text-transform: uppercase;
            text-align: center;
            border: 1px solid #1d4ed8;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }

        .highlight-row {
            background-color: #dbeafe !important;
        }

        /* Background Biru untuk kelebihan */

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Footer */
        .footer-section {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 60px;
        }

        .signature {
            text-align: center;
            width: 150px;
        }

        .sig-line {
            margin-top: 50px;
            border-top: 1px solid #000;
        }

        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8fafc;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding:8px 20px; cursor:pointer;">🖨️ Print Laporan</button>
    </div>

    <div class="container">
        <div class="header-section">
            <div class="title">
                <h2>Laporan Tagihan MRC</h2>
                <p>Periode: <?php echo e($bulan ?? ''); ?> <?php echo e($tahun ?? ''); ?></p>
            </div>
            <div class="info">
                Dicetak: <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Customer</th>
                    <th rowspan="2">SN Mesin</th>
                    <th rowspan="2">Model</th>
                    <th rowspan="2">Harga Sewa</th>
                    <th colspan="4">Black & White (BW)</th>
                    <th colspan="4">Color (CL)</th>
                    <th rowspan="2">Total Tagihan</th>
                </tr>
                <tr>
                    <th>Usage</th>
                    <th>Free</th>
                    <th>Lebih</th>
                    <th>Biaya</th>
                    <th>Usage</th>
                    <th>Free</th>
                    <th>Lebih</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php $grandTotal = 0; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tagihans ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $t = $item['tagihan'];
                        $grandTotal += $t['total'];
                        $hasExcess = $t['kelebihan_bw'] > 0 || $t['kelebihan_color'] > 0;
                    ?>
                    <tr class="<?php echo e($hasExcess ? 'highlight-row' : ''); ?>">
                        <td class="text-center"><?php echo e($i + 1); ?></td>
                        <td class="text-left"><?php echo e($item['contract']->customer?->nama_customer ?? '-'); ?></td>
                        <td class="text-center"><?php echo e($item['contract']->machine?->serial_number ?? '-'); ?></td>
                        <td class="text-center"><?php echo e($item['contract']->machine?->tipe_model ?? '-'); ?></td>
                        <td class="text-right"><?php echo e(number_format($t['harga_sewa'])); ?></td>
                        <td class="text-center"><?php echo e(number_format($t['usage_bw'])); ?></td>
                        <td class="text-center"><?php echo e(number_format($t['free_bw'])); ?></td>
                        <td class="text-center"><?php echo e($t['kelebihan_bw'] > 0 ? number_format($t['kelebihan_bw']) : '-'); ?>

                        </td>
                        <td class="text-right"><?php echo e($t['biaya_bw'] > 0 ? number_format($t['biaya_bw']) : '-'); ?></td>
                        <td class="text-center"><?php echo e(number_format($t['usage_color'])); ?></td>
                        <td class="text-center"><?php echo e(number_format($t['free_color'])); ?></td>
                        <td class="text-center">
                            <?php echo e($t['kelebihan_color'] > 0 ? number_format($t['kelebihan_color']) : '-'); ?></td>
                        <td class="text-right"><?php echo e($t['biaya_color'] > 0 ? number_format($t['biaya_color']) : '-'); ?>

                        </td>
                        <td class="text-right font-bold">Rp <?php echo e(number_format($t['total'])); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: #e2e8f0;">
                    <td colspan="13" class="text-right font-bold" style="padding:10px;">GRAND TOTAL</td>
                    <td class="text-right font-bold">Rp <?php echo e(number_format($grandTotal)); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-section">
            <div class="signature">
                <p>Kepala Cabang</p>
                <div class="sig-line"></div>
            </div>
            <div class="signature">
                <p>Dibuat oleh,</p>
                <div class="sig-line"></div>
            </div>
        </div>
    </div>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/reports/mrc-tagihan.blade.php ENDPATH**/ ?>