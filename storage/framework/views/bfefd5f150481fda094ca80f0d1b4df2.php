<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pinjam Part #<?php echo e(str_pad($header->id, 5, '0', STR_PAD_LEFT)); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A5 portrait;
            margin: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #e5e5e5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px;
            font-size: 11px;
            color: #000;
        }

        .page {
            width: 148mm;
            min-height: 210mm;
            background: #fff;
            padding: 12mm 14mm 10mm;
        }

        /* ── KOP ── */
        .kop {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            margin-bottom: 6px;
        }

        .kop-left .company-name {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-transform: uppercase;
        }

        .kop-left .company-sub {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .kop-right {
            text-align: right;
        }

        .kop-right .doc-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .kop-right .doc-no {
            font-size: 10px;
            font-weight: 500;
            margin-top: 2px;
            color: #333;
        }

        /* ── GARIS BAWAH KOP ── */
        .kop-line {
            border-top: 1px solid #000;
            margin-bottom: 10px;
        }

        /* ── INFO TABEL ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 90px;
            color: #444;
        }

        .info-table td:nth-child(2) {
            width: 10px;
            color: #444;
        }

        .info-table td:last-child {
            font-weight: 600;
        }

        /* ── JUDUL TABEL ── */
        .tabel-judul {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #333;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin-bottom: 0;
        }

        /* ── TABEL ITEM ── */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }

        table.items thead tr {
            border-bottom: 1px solid #000;
        }

        table.items thead th {
            padding: 5px 4px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #000;
        }

        table.items thead th.center {
            text-align: center;
        }

        table.items tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        table.items tbody tr:last-child {
            border-bottom: 1px solid #000;
        }

        table.items tbody td {
            padding: 5px 4px;
            color: #111;
        }

        table.items tbody td.center {
            text-align: center;
        }

        .part-code {
            font-size: 8.5px;
            color: #777;
            margin-top: 1px;
        }

        /* ── TOTAL ── */
        .total-baris {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 14px;
        }

        .total-baris table {
            font-size: 10px;
            border-collapse: collapse;
        }

        .total-baris td {
            padding: 3px 6px;
        }

        .total-baris .total-label {
            font-weight: 500;
            color: #333;
            text-align: right;
        }

        .total-baris .total-val {
            font-weight: 700;
            text-align: right;
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
        }

        /* ── TTD ── */
        .ttd-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 6px;
            border-top: 1px solid #ccc;
            padding-top: 12px;
        }

        .ttd-box {
            text-align: center;
        }

        .ttd-box .ttd-lbl {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #444;
            margin-bottom: 30px;
        }

        .ttd-box .ttd-line {
            border-top: 1px solid #000;
            padding-top: 3px;
            font-size: 9.5px;
            font-weight: 600;
        }

        /* ── CATATAN ── */
        .catatan {
            margin-top: 10px;
            border-top: 1px dashed #ccc;
            padding-top: 6px;
            font-size: 8.5px;
            color: #888;
            text-align: center;
        }

        /* ── TOMBOL LAYAR ── */
        .action-bar {
            width: 148mm;
            margin: 12px auto 0;
            display: flex;
            gap: 8px;
        }

        .btn {
            flex: 1;
            padding: 9px;
            border: 1.5px solid #000;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: #000;
            transition: background 0.15s;
        }

        .btn-print {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .btn:hover {
            opacity: 0.8;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        
        <div class="kop">
            <div class="kop-left">
                <div class="company-name">PT Dinamika Global Gemilang</div>
                <div class="company-sub"><?php echo e($header->nama_rayon); ?></div>
            </div>
            <div class="kop-right">
                <div class="doc-title">Bukti Pinjam Part</div>
                <div class="doc-no">No.
                    <?php echo e(str_pad($header->id, 5, '0', STR_PAD_LEFT)); ?>/G-CRB/<?php echo e(\Carbon\Carbon::parse($header->created_at)->format('m/Y')); ?>

                </div>
            </div>
        </div>
        <div class="kop-line"></div>

        
        <table class="info-table">
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?php echo e(\Carbon\Carbon::parse($header->created_at)->locale('id')->setTimezone('Asia/Jakarta')->isoFormat('D MMMM Y')); ?>

                </td>
            </tr>
            <tr>
                <td>Nama Teknisi</td>
                <td>:</td>
                <td><?php echo e($header->nama_technician); ?></td>
            </tr>
            <tr>
                <td>Jumlah Item</td>
                <td>:</td>
                <td><?php echo e($items->count()); ?> jenis sparepart</td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($header->keterangan): ?>
                <tr>
                    <td>Keterangan</td>
                    <td>:</td>
                    <td><?php echo e($header->keterangan); ?></td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>

        
        <table class="items">
            <thead>
                <tr>
                    <th style="width:24px;">No</th>
                    <th>Nama Sparepart</th>
                    <th class="center" style="width:40px;">Jml Pinjam</th>
                    <th class="center"
                        style="width:75px; background-color: #fef08a; color: #854d0e; border-left: 1px solid #e2e8f0;">
                        Total Tas Teknisi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($i + 1); ?>.</td>
                        <td>
                            <div><?php echo e($item->nama_sparepart); ?></div>
                            <div class="part-code"><?php echo e($item->code_part ?? '-'); ?></div>
                        </td>
                        <td class="center" style="font-weight:700;"><?php echo e($item->jumlah); ?></td>
                        <td class="center"
                            style="font-weight:700; background-color: #fefcf0; border-left: 1px solid #e2e8f0;">
                            <?php echo e($saldoTeknisi[$item->sparepart_id] ?? 0); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>

        
        <div class="total-baris">
            <table>
                <tr>
                    <td class="total-label">Total Pcs Dipinjam</td>
                    <td style="width:8px; text-align:center;">:</td>
                    <td class="total-val"><?php echo e($items->sum('jumlah')); ?> pcs</td>
                </tr>
            </table>
        </div>

        
        <div class="ttd-section">
            <div class="ttd-box">
                <div class="ttd-lbl">Diserahkan Oleh</div>
                <div class="ttd-line">( Gudang )</div>
            </div>
            <div class="ttd-box">
                <div class="ttd-lbl">Diterima Oleh</div>
                <div class="ttd-line">( <?php echo e($header->nama_technician); ?> )</div>
            </div>
        </div>

        <div class="catatan">
            Dokumen ini merupakan bukti pengambilan sparepart dari gudang. Harap disimpan dengan baik.<br>
            Dicetak: <?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm')); ?> WIB
        </div>

    </div>

    <div class="action-bar">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn" onclick="window.close()">✕ Tutup</button>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(() => window.print(), 500);
        });
    </script>

</body>

</html>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/print/bukti-pinjam-multi.blade.php ENDPATH**/ ?>