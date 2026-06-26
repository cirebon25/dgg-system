<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>PO Part - <?php echo e($po->no_po); ?></title>
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
            margin-bottom: 16px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
            font-size: 11px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            border: 1px solid #333;
            padding: 5px 6px;
            background: #d0d0d0;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }

        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            font-size: 11px;
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
                padding: 12mm 8mm;
            }

            @page {
                size: A5 Landscape;
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
            <h2>Permintaan Sparepart / Mesin</h2>
            
            <p>Tanggal &nbsp;: <?php echo e(\Carbon\Carbon::parse($po->tanggal)->isoFormat('D MMMM YYYY')); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($po->keterangan): ?>
                <p>Keterangan: <?php echo e($po->keterangan); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <hr>

        <table>
            <thead>
                <tr>
                    <th class="center" style="width:34px;">No</th>
                    <th>Nama Part</th>
                    <th style="width:110px;">Merk / Type</th>
                    <th style="width:110px;">Kode Part</th>
                    <th class="center" style="width:60px;">Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="center"><?php echo e($i + 1); ?></td>
                        <td><?php echo e($item->nama_part); ?></td>
                        <td><?php echo e($item->merk_type ?? '-'); ?></td>
                        <td><?php echo e($item->kode_part ?? '-'); ?></td>
                        <td class="center"><?php echo e($item->jumlah); ?></td>
                        <td><?php echo e($item->keterangan ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="center" style="padding:16px; color:#888;">
                            Tidak ada item.
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total Item</td>
                    <td class="center"><?php echo e($po->items->sum('jumlah')); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p> </p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
            <div class="ttd">
                <p>Cirebon, <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM YYYY')); ?></p>
                <p>Dibuat oleh,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>
    </div>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/reports/po-part-print.blade.php ENDPATH**/ ?>