<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penyebaran Seri Mesin di Customer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            padding: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            color: #444;
            margin-top: 3px;
        }

        .divider {
            border-top: 2px solid #000;
            margin-top: 10px;
            border-bottom: 1px solid #000;
            height: 3px;
        }

        /* Layout Ringkasan Card */
        .summary-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 25px;
        }

        .summary-card {
            border: 1px solid #000;
            padding: 12px;
            border-radius: 6px;
            background-color: #fafafa;
            text-align: center;
        }

        .summary-card .title {
            font-size: 11px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        /* Komponen Pencarian */
        .filter-section {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
        }

        .search-input {
            width: 250px;
            padding: 8px 12px;
            font-size: 11px;
            border: 1px solid #000;
            border-radius: 4px;
            outline: none;
        }

        .filter-select {
            padding: 8px 12px;
            font-size: 11px;
            border: 1px solid #000;
            border-radius: 4px;
            background: #fff;
            outline: none;
        }

        .btn-print {
            padding: 8px 15px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
        }

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
            font-weight: bold;
        }

        .row-total {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .empty-cell {
            color: #ccc;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 10px;
            }

            .summary-card {
                background-color: #fff !important;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>PT. DINAMIKA GLOBAL GEMILANG</h2>
        <h2>REKAPITULASI PENYEBARAN UNIT SERI MESIN</h2>
        <p>Status Penempatan Aktif (Sewa) Per Tanggal: <?php echo e(date('d/m/Y')); ?></p>
        <div class="divider"></div>
    </div>

    
    <?php
        $totalPerSeri = array_fill_keys($listSeri->toArray(), 0);
        $grandTotalSeluruh = 0;
        foreach ($matrix as $item) {
            foreach ($listSeri as $seri) {
                $totalPerSeri[$seri] += $item['seri'][$seri];
            }
            $grandTotalSeluruh += $item['total_per_customer'];
        }
    ?>

    <div class="summary-container">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $listSeri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="summary-card">
                <div class="title">TOTAL <?php echo e($seri); ?></div>
                <div class="value"><?php echo e($totalPerSeri[$seri]); ?> Unit</div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="summary-card" style="border: 2px solid #16a34a; background-color: #e6f4ea;">
            <div class="title" style="color: #15803d;">GRAND TOTAL UNIT</div>
            <div class="value" style="color: #15803d;"><?php echo e($grandTotalSeluruh); ?> Unit</div>
        </div>
    </div>

    
    <div class="no-print"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <div class="filter-section">
            <input type="text" id="searchInput" class="search-input" onkeyup="jalankanFilter()"
                placeholder="🔍 Cari nama customer...">

            <select id="filterSeri" class="filter-select" onchange="jalankanFilter()">
                <option value="">-- Semua Seri Mesin --</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $listSeri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($seri); ?>"><?php echo e($seri); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan / Save PDF</button>
        </div>
    </div>

    <table id="reportTable">
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th class="text-left">Nama Customer</th>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $listSeri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th style="width: 90px;"><?php echo e($seri); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <th style="width: 100px;">Total Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $matrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="customer-row" data-seri-list='<?php echo json_encode($item['seri'], 15, 512) ?>'>
                    <td class="text-center row-number"><?php echo e($no++); ?></td>
                    <td class="text-left font-bold customer-name"><?php echo e($item['nama_customer']); ?></td>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $listSeri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $jumlah = $item['seri'][$seri]; ?>
                        <td class="text-center <?php echo e($jumlah > 0 ? 'font-bold' : 'empty-cell'); ?>">
                            <?php echo e($jumlah > 0 ? $jumlah . ' Unit' : '-'); ?>

                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <td class="text-center row-total"><?php echo e($item['total_per_customer']); ?> Unit</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(count($listSeri) + 3); ?>" class="text-center" style="padding: 20px; color: #666;">
                        Tidak ada data penempatan mesin aktif saat ini.
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($matrix)): ?>
                <tr class="row-total" id="totalRow">
                    <td colspan="2" class="text-right">TOTAL UNIT TERPASANG:</td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $listSeri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-center"><?php echo e($totalPerSeri[$seri]); ?> Unit</td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <td class="text-center" style="background-color: #e6f4ea; border: 2px solid #16a34a;">
                        <?php echo e($grandTotalSeluruh); ?> Unit
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    
    <script>
        function jalankanFilter() {
            let searchFilter = document.getElementById("searchInput").value.toUpperCase();
            let seriFilter = document.getElementById("filterSeri").value;
            let rows = document.getElementsByClassName("customer-row");
            let currentNo = 1;

            for (let i = 0; i < rows.length; i++) {
                let nameCell = rows[i].getElementsByClassName("customer-name")[0];
                let seriData = JSON.parse(rows[i].getAttribute("data-seri-list"));

                let matchNama = false;
                let matchSeri = false;

                // 1. Cek Filter Nama
                if (nameCell) {
                    let txtValue = nameCell.textContent || nameCell.innerText;
                    if (txtValue.toUpperCase().indexOf(searchFilter) > -1) {
                        matchNama = true;
                    }
                }

                // 2. Cek Filter Tipe Seri Mesin
                if (seriFilter === "") {
                    matchSeri = true; // Jika pilih semua
                } else {
                    if (seriData[seriFilter] && seriData[seriFilter] > 0) {
                        matchSeri = true; // Jika customer punya mesin tipe tersebut
                    }
                }

                // Tampilkan jika kedua filter terpenuhi
                if (matchNama && matchSeri) {
                    rows[i].style.display = "";
                    rows[i].getElementsByClassName("row-number")[0].innerText = currentNo++;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/reports/rekap-mesin-customer.blade.php ENDPATH**/ ?>