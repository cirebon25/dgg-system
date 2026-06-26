<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pemakaian Sparepart - <?php echo e($bulan); ?> <?php echo e($tahun); ?></title>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #333;
            padding: 5px 6px;
            background: #d0d0d0;
            font-weight: bold;
            text-align: left;
        }

        td {
            border: 1px solid #333;
            padding: 4px 6px;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        tfoot td {
            font-weight: bold;
            background: #e0e0e0;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #888;
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
                size: A4 portrait;
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
            <h2>Laporan Pemakaian Sparepart Bulanan</h2>
            <p>Periode &nbsp;&nbsp;: <?php echo e($bulan); ?> <?php echo e($tahun); ?></p>
            <p>Dicetak &nbsp;&nbsp;: <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB</p>
        </div>
        <hr>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data->isEmpty()): ?>
            <div class="no-data">
                Tidak ada pemakaian sparepart di periode ini, atau snapshot bulan lalu belum tersedia.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th class="center" style="width:30px;">No</th>
                        <th>Nama Sparepart</th>
                        <th style="width:90px;">Kode Part</th>
                        <th class="center" style="width:80px;">Stok Awal</th>
                        <th class="center" style="width:80px;">Stok Akhir</th>
                        <th class="center" style="width:90px;">Pemakaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="center"><?php echo e($i + 1); ?></td>
                            <td><?php echo e($item['sparepart']->nama_sparepart); ?></td>
                            <td><?php echo e($item['sparepart']->code_part ?? '-'); ?></td>
                            <td class="center"><?php echo e($item['stok_awal']); ?></td>
                            <td class="center"><?php echo e($item['stok_akhir']); ?></td>
                            <td class="center" style="font-weight:bold;"><?php echo e($item['pemakaian']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">Total Pemakaian</td>
                        <td class="center"><?php echo e($data->sum('pemakaian')); ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Kepala Gudang</p>
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
<?php /**PATH C:\laragon\www\dgg-system\resources\views/reports/sparepart-pemakaian-bulanan.blade.php ENDPATH**/ ?>