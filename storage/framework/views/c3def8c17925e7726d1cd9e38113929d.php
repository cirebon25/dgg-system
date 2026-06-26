<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saldo Sparepart — PT DGG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --grey-hd:   #969494;
            --grey-sub:  #5a5a5a;
            --grey-line: #c0bfbc;
            --ink:       #1c1b18;
            --ink-muted: #7a7870;
            --surface:   #ffffff;
            --bg:        #efefed;
            --red-bg:    #fdecea;
            --red-text:  #8b2217;
            --font:      'Source Sans 3', sans-serif;
            --font-ser:  'Source Serif 4', serif;
        }

        body { font-family: var(--font); background: var(--bg); color: var(--ink); font-size: 13px; }

        /* ── TOOLBAR ── */
        .toolbar {
            background: var(--grey-hd);
            color: #ddd;
            padding: 9px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .toolbar-hint { color: #aaa; }
        .btn { padding: 6px 18px; border-radius: 2px; font-weight: 700; font-size: 12px; font-family: var(--font); cursor: pointer; border: none; }
        .btn-print { background: #b8860b; color: #fff; margin-right: 6px; }
        .btn-close  { background: #555; color: #ddd; }

        /* ── PAGE ── */
        .page { max-width: 940px; margin: 0 auto; padding: 22px 18px 36px; }

        /* ── KOP SURAT ── */
        .kop {
            background: var(--surface);
            border: 1.5px solid #888;
            margin-bottom: 8px;
        }
        .kop-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px 12px;
            border-bottom: 2px solid var(--grey-hd);
        }
        .kop-title {
            font-family: var(--font-ser);
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--ink);
        }
        .kop-sub { font-size: 11px; color: var(--ink-muted); margin-top: 3px; }
        .kop-meta { text-align: right; font-size: 11px; color: var(--ink-muted); line-height: 1.9; }
        .kop-meta strong { color: var(--ink); font-size: 12px; font-weight: 700; }
        .kop-banner {
            background: var(--grey-hd);
            color: #e0e0e0;
            text-align: center;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 5px 0;
        }

        /* ── STATS CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }
        .stat-card {
            background: var(--surface);
            border: 1.5px solid var(--grey-line);
            border-top: 3px solid var(--grey-hd);
            padding: 10px 14px 9px;
        }
        .stat-card.sc-kosong { border-top-color: var(--red-text); }
        .stat-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .9px;
            text-transform: uppercase;
            color: var(--ink-muted);
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e0e0e0;
        }
        .stat-value {
            font-family: var(--font-ser);
            font-size: 22px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.1;
        }
        .sc-kosong .stat-value { color: var(--red-text); }
        .stat-unit { font-size: 10px; color: var(--ink-muted); margin-top: 3px; }

        /* ── FORMULA ── */
        .formula {
            background: #f9f8f4;
            border: 1px solid var(--grey-line);
            padding: 6px 14px;
            font-size: 11.5px;
            color: var(--ink-muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .fx-label { font-weight: 700; font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: #888; margin-right: 4px; }
        .f-bold { color: var(--ink); font-weight: 700; }
        .f-op   { color: #bbb; margin: 0 4px; font-size: 14px; }

        /* ── TABLE ── */
        .table-wrap { background: var(--surface); border: 1.5px solid #888; }
        table { width: 100%; border-collapse: collapse; }

        /* Header row 1 */
        thead tr.h1 { background: var(--grey-hd); color: #e8e8e8; }
        thead tr.h1 th {
            padding: 9px 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-right: 1px solid #555;
            white-space: nowrap;
        }
        thead tr.h1 th:last-child { border-right: none; }
        .th-saldo-span { text-align: center !important; border-bottom: 1px solid #555 !important; }

        /* Header row 2 */
        thead tr.h2 { background: var(--grey-sub); }
        thead tr.h2 th {
            padding: 5px 10px;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: .8px;
            text-transform: uppercase;
            text-align: center;
            border-right: 1px solid #6e6e6e;
            color: #d0d0d0;
        }
        thead tr.h2 th:last-child { border-right: none; }

        /* Body */
        tbody tr { border-bottom: 1px solid #e8e6e0; }
        tbody tr:nth-child(even) { background: #f8f8f6; }
        tbody tr:last-child { border-bottom: none; }

        /* Baris stok kosong (total = 0) */
        tbody tr.row-empty { background: var(--red-bg) !important; }
        tbody tr.row-empty td { color: var(--red-text); }

        td {
            padding: 7px 10px;
            font-size: 12px;
            vertical-align: middle;
            border-right: 1px solid #e8e6e0;
        }
        td:last-child { border-right: none; }

        .td-no     { text-align: center; font-size: 11px; color: var(--ink-muted); width: 4%; }
        .td-nopart { text-align: center; font-weight: 700; font-size: 12px; width: 15%; }
        .td-kode   { font-size: 11px; color: var(--ink-muted); width: 16%; }
        .td-nama   { font-size: 12px; font-weight: 500; line-height: 1.4; }

        .td-stok {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            width: 10%;
        }
        .td-stok small { font-size: 9px; font-weight: 400; opacity: .6; }

        .badge-kosong {
            display: inline-block;
            background: var(--red-text);
            color: #fff;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .5px;
            padding: 1px 5px;
            border-radius: 2px;
            margin-left: 5px;
            vertical-align: middle;
            text-transform: uppercase;
        }

        /* Kolom Total — garis kiri tipis saja, tanpa bg */
        .col-total { border-left: 2px solid #aaa !important; }

        /* ── GRAND TOTAL ── */
        tfoot { display: none; }
        @media print { tfoot { display: table-footer-group !important; } }

        tfoot tr { background: #e8e8e8; border-top: 2px solid var(--grey-hd); }
        tfoot td { padding: 9px 10px; font-size: 12px; font-weight: 700; }
        .tfoot-label {
            font-size: 11px; font-weight: 700; letter-spacing: .5px;
            text-transform: uppercase; text-align: right; color: var(--ink-muted);
        }

        /* Grand total versi screen */
        .grand-total-screen {
            background: var(--surface);
            border: 1.5px solid #888;
            border-top: none;
            display: flex;
            align-items: center;
        }
        .gt-label {
            flex: 1; padding: 10px 14px;
            font-size: 11px; font-weight: 700; letter-spacing: .5px;
            text-transform: uppercase; color: var(--ink-muted);
            text-align: right; border-right: 1px solid var(--grey-line);
        }
        .gt-cell {
            padding: 10px 16px; text-align: center;
            font-size: 14px; font-weight: 700;
            width: 12%; border-right: 1px solid var(--grey-line);
            color: var(--ink);
        }
        .gt-cell:last-child { border-right: none; border-left: 2px solid #aaa; }
        .gt-cell small { font-size: 9px; font-weight: 400; color: var(--ink-muted); display: block; margin-top: 2px; }

        /* ── PRINT ── */
        @media print {
            .toolbar { display: none !important; }
            .grand-total-screen { display: none !important; }
            body { background: #fff; }
            .page { padding: 5mm 5mm; max-width: 100%; }
            .kop { border: 1.5px solid #555; }
            .table-wrap { border: 1.5px solid #555; }
            thead tr.h1 { background: var(--grey-hd) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead tr.h2 { background: var(--grey-sub) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr.row-empty { background: var(--red-bg) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tfoot tr { background: #e8e8e8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr { page-break-inside: avoid; }
            td { padding: 5px 8px; font-size: 10.5px; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="toolbar">
        <div class="toolbar-hint">💡 Dialog cetak otomatis muncul &nbsp;|&nbsp; PT DGG — Laporan Saldo Gudang Sparepart</div>
        <div>
            <button onclick="window.print()" class="btn btn-print">🖨️ &nbsp;Cetak Sekarang</button>
            <button onclick="window.close()"  class="btn btn-close">✕ Tutup</button>
        </div>
    </div>

    <div class="page">

        
        <div class="kop">
            <div class="kop-head">
                <div>
                    <div class="kop-title">PT. Dinamika Global Gemilang</div>
                    <div class="kop-sub">Depo Cirebon &nbsp;&middot;&nbsp; Divisi Gudang &amp; Sparepart</div>
                </div>
                <div class="kop-meta">
                    <div>Tanggal Cetak</div>
                    <strong><?php echo e($tanggalCetak); ?> WIB</strong>
                    <div style="margin-top:4px;">Total Item Terdaftar</div>
                    <strong><?php echo e($summary['total_item']); ?> Part</strong>
                </div>
            </div>
            <div class="kop-banner">Laporan Saldo Gudang Sparepart &mdash; Data Realtime</div>
        </div>

        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Saldo Aktif (Gudang)</div>
                <div class="stat-value"><?php echo e(number_format($summary['total_aktif'])); ?></div>
                <div class="stat-unit">Pcs &mdash; stok di gudang</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Saldo Tas Teknisi</div>
                <div class="stat-value"><?php echo e(number_format($summary['total_tas'])); ?></div>
                <div class="stat-unit">Pcs &mdash; dibawa teknisi</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Saldo Keseluruhan</div>
                <div class="stat-value"><?php echo e(number_format($summary['total_saldo'])); ?></div>
                <div class="stat-unit">Pcs &mdash; gabungan</div>
            </div>
            <div class="stat-card sc-kosong">
                <div class="stat-label">Item Stok Kosong</div>
                <div class="stat-value"><?php echo e($summary['item_kosong']); ?></div>
                <div class="stat-unit">Part &mdash; total saldo = 0</div>
            </div>
        </div>

        
        <div class="formula">
            <span class="fx-label">Rumus :</span>
            <span class="f-bold">Saldo Aktif (Gudang)</span>
            <span class="f-op">+</span>
            <span class="f-bold">Saldo Tas Teknisi</span>
            <span class="f-op">=</span>
            <span class="f-bold">Total Saldo Keseluruhan</span>
        </div>

        
        <div class="table-wrap">
            <table>
                <thead>
                    <tr class="h1">
                        <th rowspan="2" style="text-align:center; width:4%;">No</th>
                        <th rowspan="2" style="text-align:center; width:15%;">No Part</th>
                        <th rowspan="2" style="width:16%;">Kode Part</th>
                        <th rowspan="2">Nama Sparepart</th>
                        <th colspan="3" class="th-saldo-span" style="width:33%;">S &nbsp; A &nbsp; L &nbsp; D &nbsp; O</th>
                    </tr>
                    <tr class="h2">
                        <th style="width:11%;">Aktif (Gudang)</th>
                        <th style="width:11%;">Tas Teknisi</th>
                        <th style="width:11%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $spareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $isEmpty = $part->total_saldo <= 0; ?>
                        <tr class="<?php echo e($isEmpty ? 'row-empty' : ''); ?>">
                            <td class="td-no"><?php echo e($index + 1); ?></td>
                            <td class="td-nopart"><?php echo e($part->no_part); ?></td>
                            <td class="td-kode"><?php echo e($part->code_part ?? '-'); ?></td>
                            <td class="td-nama">
                                <?php echo e(strtoupper($part->nama_sparepart ?? '-')); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEmpty): ?>
                                    <span class="badge-kosong">Kosong</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="td-stok"><?php echo e(number_format($part->stok_aktif)); ?> <small>Pcs</small></td>
                            <td class="td-stok"><?php echo e(number_format($part->stok_tas)); ?> <small>Pcs</small></td>
                            <td class="td-stok col-total"><?php echo e(number_format($part->total_saldo)); ?> <small>Pcs</small></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px; color:var(--ink-muted); font-size:13px;">
                                Belum ada data sparepart di gudang.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="tfoot-label">Grand Total &mdash; <?php echo e($summary['total_item']); ?> Part :</td>
                        <td class="td-stok"><?php echo e(number_format($summary['total_aktif'])); ?> <small>Pcs</small></td>
                        <td class="td-stok"><?php echo e(number_format($summary['total_tas'])); ?> <small>Pcs</small></td>
                        <td class="td-stok col-total"><?php echo e(number_format($summary['total_saldo'])); ?> <small>Pcs</small></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        
        <div class="grand-total-screen">
            <div class="gt-label">Grand Total &mdash; <?php echo e($summary['total_item']); ?> Part :</div>
            <div class="gt-cell"><?php echo e(number_format($summary['total_aktif'])); ?><small>Aktif (Gudang)</small></div>
            <div class="gt-cell"><?php echo e(number_format($summary['total_tas'])); ?><small>Tas Teknisi</small></div>
            <div class="gt-cell"><?php echo e(number_format($summary['total_saldo'])); ?><small>Total Saldo</small></div>
        </div>

    </div>
</body>
</html><?php /**PATH C:\laragon\www\dgg-system\resources\views/sparepart/saldo.blade.php ENDPATH**/ ?>