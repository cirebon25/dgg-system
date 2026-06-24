<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Rolling DGG</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
        }

        /* Layout Header */
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }

        .logo-area {
            width: 60%;
            text-align: left;
            vertical-align: top;
        }

        .no-sj-area {
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .customer-info {
            margin-bottom: 15px;
        }

        .customer-info strong {
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Unified Table Full Border */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .bg-gray {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }

        .ttd-table td {
            height: 70px;
            vertical-align: top;
            width: 33%;
            border: none;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td class="logo-area">
                <strong style="font-size: 18px;">PT DINAMIKA GLOBAL GEMILANG</strong><br>
                <small>JL. PULASAREN NO 56B. PULASAREN-PEKALIPAN CIREBON </small><br>
                <small>Telp : (0231) 202020</small>
            </td>
            <td class="no-sj-area">
                <strong style="font-size: 14px; text-decoration: underline;">SURAT JALAN TUKAR MESIN</strong><br>
                <span>No: <?php echo e($nomor_sj); ?></span><br>
                <span>Tanggal: <?php echo e($tanggal); ?></span>
            </td>
        </tr>
    </table>

    
    <div class="customer-info">
        Kepada Yth:<br>
        <strong><?php echo e($replacement->customer?->nama_customer ?? '-'); ?></strong><br>
        <span><?php echo e($replacement->customer?->alamat ?? '-'); ?></span>
    </div>

    <table class="main-table">
        <thead>
            <tr class="bg-gray">
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Deskripsi Barang / Unit</th>
                <th style="width: 16%;">No Seri</th>
                <th style="width: 16%;">Type Model</th>
                <th style="width: 16%;">Qty / Counter</th>
                <th style="width: 22%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <td>1</td>
                <td class="text-left" style="font-weight: bold; font-style: italic;">PENARIKAN UNIT (LAMA)</td>
                <td><strong><?php echo e($replacement->machine_old?->serial_number ?? '-'); ?></strong></td>
                <td><?php echo e($replacement->machine_old?->tipe_model ?? '-'); ?></td>
                <td>
                    BW: <?php echo e(number_format($replacement->counter_bw_final ?? 0)); ?><br>
                    CL: <?php echo e(number_format($replacement->counter_color_final ?? 0)); ?>

                </td>
                <td class="text-left"><?php echo e($replacement->keterangan ?? '-'); ?></td>
            </tr>
            
            <tr>
                <td>2</td>
                <td class="text-left" style="font-weight: bold; font-style: italic;">PENGIRIMAN UNIT (BARU)</td>
                <td><strong><?php echo e($replacement->machine_new?->serial_number ?? '-'); ?></strong></td>
                <td><?php echo e($replacement->machine_new?->tipe_model ?? '-'); ?></td>
                <td>
                    1 Unit<br>
                    BW: <?php echo e(number_format($replacement->deployment?->counter_bw ?? 0)); ?> /
                    CL: <?php echo e(number_format($replacement->deployment?->counter_color ?? 0)); ?>

                </td>
                <td class="text-left">-</td>
            </tr>

            
            <?php $noBaris = 3; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $replacement->deployment?->deploymentSpareparts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($noBaris++); ?></td>
                    <td class="text-left">
                        <?php echo e($ds->sparepart?->nama_alias ?: $ds->sparepart?->nama_sparepart ?? 'Sparepart'); ?></td>
                    <td>-</td>
                    <td>-</td>
                    <td><?php echo e($ds->jumlah); ?> Pcs</td>
                    <td class="text-left">-</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                Hormat Kami,<br><br><br><br><br>
                ( ___________________ )
            </td>
            <td>
                Teknisi Pelaksana,<br><br><br><br><br>
                ( <?php echo e($replacement->technician?->nama_technician ?? '___________________'); ?> )
            </td>
            <td>
                Penerima / Customer,<br><br><br><br><br>
                ( ___________________ )
            </td>
        </tr>
    </table>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/cetak/surat-jalan-rolling.blade.php ENDPATH**/ ?>