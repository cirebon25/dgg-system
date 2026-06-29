<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pengeluaran Kas/Bank</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 210mm;
            height: 148mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #111;
            background: #fff;
        }

        /* Bingkai luar memenuhi kertas dengan margin presisi */
        .sheet {
            width: 210mm;
            height: 148mm;
            padding: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .outer {
            border: 1.6px solid #000;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header-row {
            display: flex;
            border-bottom: 1.2px solid #000;
            flex: 0 0 auto;
        }

        .header-logo {
            width: 17mm;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #000;
            padding: 3mm 0;
        }

        .header-company {
            flex: 1;
            padding: 3mm 4mm;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.2mm;
        }

        .company-name {
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .company-addr {
            font-size: 8.5px;
            font-weight: 500;
            color: #222;
        }

        .header-novoucher {
            width: 46mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* JUDUL */
        .judul-row {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
            letter-spacing: 0.4px;
            padding: 2.5mm 0;
            border-bottom: 1.2px solid #000;
            flex: 0 0 auto;
        }

        /* DIBAYAR KEPADA */
        .kepada-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.2px solid #000;
            padding: 2mm 4mm;
            font-size: 10px;
            flex: 0 0 auto;
        }

        /* TABEL URAIAN — mengisi sisa ruang secara proporsional */
        .uraian-wrap {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            border-bottom: 1.2px solid #000;
            overflow: hidden;
        }

        .uraian-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .uraian-table thead tr th {
            border-bottom: 1.2px solid #000;
            border-right: 1px solid #000;
            padding: 1.5mm 3mm;
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            background: #f3f3f3;
        }

        .uraian-table thead tr th:last-child {
            border-right: none;
        }

        .uraian-table tbody tr td {
            border-right: 1px solid #000;
            padding: 1.2mm 3mm;
            font-size: 10px;
            vertical-align: top;
        }

        .uraian-table tbody tr td:last-child {
            border-right: none;
        }

        .uraian-table tbody tr:not(:last-child) td {
            border-bottom: 0.6px solid #ddd;
        }

        .col-uraian {
            width: auto;
            text-align: left;
        }

        .col-kode {
            width: 38mm;
            text-align: center !important;
        }

        .col-jumlah {
            width: 32mm;
            text-align: right !important;
        }

        .vehicle-detail {
            margin-top: 1mm;
        }

        .vehicle-detail div {
            font-size: 8px;
            color: #444;
            font-style: italic;
            line-height: 1.5;
            white-space: nowrap;
        }

        /* TERBILANG + DIBAYAR DENGAN */
        .terbilang-block {
            display: flex;
            justify-content: space-between;
            padding: 2.5mm 4mm;
            border-bottom: 1.2px solid #000;
            flex: 0 0 auto;
            gap: 6mm;
        }

        .terbilang-left {
            flex: 1;
            font-size: 10px;
        }

        .terbilang-line {
            display: flex;
            gap: 5px;
            margin-bottom: 2.5mm;
        }

        .tb-label {
            font-weight: 700;
            min-width: 17mm;
        }

        .tb-val {
            font-weight: 700;
            font-style: italic;
        }

        .bayar-dengan-label {
            font-size: 10px;
            margin-bottom: 1.5mm;
        }

        .bayar-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 1.5mm;
            font-size: 10px;
        }

        .bayar-lbl {
            min-width: 30mm;
        }

        .terbilang-right {
            min-width: 48mm;
            font-size: 9.5px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .bayar-right-row {
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 1.5mm;
        }

        .bayar-right-val {
            border-bottom: 1px solid #000;
            min-width: 22mm;
            display: inline-block;
            height: 3mm;
        }

        .jumlah-row {
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 5px;
            margin-top: 1.5mm;
            font-weight: 700;
            font-size: 11.5px;
        }

        .jumlah-val {
            border-bottom: 2.2px double #000;
            min-width: 24mm;
            text-align: right;
            padding-bottom: 0.5mm;
        }

        /* TTD */
        .ttd-row {
            display: flex;
            flex: 0 0 auto;
            height: 22mm;
        }

        .ttd-cell {
            flex: 1;
            text-align: center;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 10px;
            border-right: 1px solid #000;
        }

        .ttd-cell:last-child {
            border-right: none;
        }

        .ttd-name {
            margin-top: auto;
            font-weight: 700;
            padding-bottom: 1.5mm;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="outer">

            
            <div class="header-row">
                <div class="header-logo">
                    <svg width="13mm" height="13mm" viewBox="0 0 58 58" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <radialGradient id="g1" cx="38%" cy="32%" r="70%">
                                <stop offset="0%" stop-color="#64B5F6" />
                                <stop offset="48%" stop-color="#1565C0" />
                                <stop offset="100%" stop-color="#0D47A1" />
                            </radialGradient>
                        </defs>
                        <circle cx="29" cy="29" r="27.5" fill="url(#g1)" stroke="#888"
                            stroke-width="1.2" />
                        <path d="M29,29 L53,42 A27.5,27.5 0 0,1 12,54 Z" fill="#B71C1C" opacity="0.88" />
                        <circle cx="29" cy="29" r="19" fill="none" stroke="rgba(255,255,255,0.4)"
                            stroke-width="2" />
                        <text x="29" y="34" text-anchor="middle" fill="#fff" font-family="Arial Black,Arial"
                            font-weight="900" font-size="11.5" letter-spacing="-0.5">PT DGG</text>
                    </svg>
                </div>
                <div class="header-company">
                    <div class="company-name">PT. DINAMIKA GLOBAL GEMILANG</div>
                    <div class="company-addr">JL. GURAME NO. 20 KOTA BANDUNG 40262</div>
                    <div class="company-addr">JL. PULASAREN NO.56B PEKALIPAN KOTA CIREBON 45116</div>
                </div>
                <div class="header-novoucher"><?php echo e($noVoucher); ?></div>
            </div>

            
            <div class="judul-row">BUKTI PENGELUARAN KAS/BANK</div>

            
            <div class="kepada-row">
                <span>Dibayar Kepada :&nbsp; ……………………………………………………………………………</span>
                <span><?php echo e($tanggalStr); ?></span>
            </div>

            
            <div class="uraian-wrap">
                <table class="uraian-table">
                    <thead>
                        <tr>
                            <th class="col-uraian">U r a i a n</th>
                            <th class="col-kode">Kode perkiraan</th>
                            <th class="col-jumlah">Jumlah (Rp.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="col-uraian">
                                    <?php echo e(strtoupper($item->uraian ?? '')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->plat_nomor) || !empty($item->km_awal) || !empty($item->km_akhir)): ?>
                                        <div class="vehicle-detail">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->plat_nomor)): ?>
                                                <div>Plat No&nbsp;&nbsp;&nbsp;: <?php echo e(strtoupper($item->plat_nomor)); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->km_awal)): ?>
                                                <div>KM Awal&nbsp;&nbsp;:
                                                    <?php echo e(number_format($item->km_awal, 0, ',', '.')); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->km_akhir)): ?>
                                                <div>KM Akhir&nbsp;: <?php echo e(number_format($item->km_akhir, 0, ',', '.')); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="col-kode"><?php echo e($item->kode_perkiraan ?? ''); ?></td>
                                <td class="col-jumlah">
                                    <?php echo e($item->jumlah ? number_format($item->jumlah, 0, ',', '.') : ''); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="terbilang-block">
                <div class="terbilang-left">
                    <div class="terbilang-line">
                        <span class="tb-label">Terbilang</span>
                        <span> :</span>
                        <span class="tb-val"># <?php echo e($terbilang); ?> #</span>
                    </div>

                    <div class="bayar-dengan-label">Dibayar dengan;</div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Uang Tunai</span>
                        <span>:</span>
                        <span style="font-size:14px; font-weight:bold; line-height:1;">✓</span>
                    </div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Cek/giro bilyet Bank</span>
                        <span>:</span>
                        <span style="border-bottom:1px solid #000; min-width:28mm; display:inline-block;">&nbsp;</span>
                    </div>
                </div>

                <div class="terbilang-right">
                    <div class="bayar-right-row">
                        <span>Rp.</span>
                        <span class="bayar-right-val"></span>
                    </div>
                    <div class="bayar-right-row">
                        <span>No._______</span>
                        <span>Rp.</span>
                        <span class="bayar-right-val"></span>
                    </div>
                    <div class="jumlah-row">
                        <span>Jumlah Rp.</span>
                        <span class="jumlah-val"><?php echo e($total > 0 ? number_format($total, 0, ',', '.') : '0'); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="ttd-row">
                <div class="ttd-cell">Dibuat Oleh,<div class="ttd-name">( <?php echo e($data->pembuat ?? 'RUDI'); ?> )</div>
                </div>
                <div class="ttd-cell">Diketahui Oleh,<div class="ttd-name">( <?php echo e($data->pemeriksa ?? 'RIZEN'); ?> )</div>
                </div>
                <div class="ttd-cell">Disetujui,<div class="ttd-name">( .................... )</div>
                </div>
                <div class="ttd-cell">Penerima,<div class="ttd-name">( <?php echo e($data->penerima ?? 'RUDI'); ?> )</div>
                </div>
            </div>

        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/print/cash-mutation.blade.php ENDPATH**/ ?>