<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pinjam Part #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;600;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A5 portrait;
            margin: 0;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .page {
            width: 148mm;
            min-height: 210mm;
            background: #fff;
            padding: 12mm 14mm;
            position: relative;
            overflow: hidden;
        }

        /* ── DEKORASI BACKGROUND ── */
        .page::before {
            content: 'PINJAM';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 72px;
            font-weight: 700;
            color: rgba(29, 78, 216, 0.04);
            letter-spacing: 0.15em;
            pointer-events: none;
            white-space: nowrap;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-left .company {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #111827;
            line-height: 1.2;
        }
        .header-left .rayon {
            font-size: 11px;
            color: #6B7280;
            margin-top: 3px;
        }
        .header-right {
            text-align: right;
        }
        .header-right .doc-badge {
            display: inline-block;
            background: #1D4ED8;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 3px;
            margin-bottom: 5px;
        }
        .header-right .no-transaksi {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            margin-bottom: 14px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .info-item .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9CA3AF;
            margin-bottom: 2px;
        }
        .info-item .value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        .info-item .value.mono {
            font-family: 'IBM Plex Mono', monospace;
        }

        /* ── PART DETAIL ── */
        .section-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9CA3AF;
            margin-bottom: 6px;
        }
        .part-card {
            border: 1.5px solid #1D4ED8;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .part-card .part-info .part-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            line-height: 1.3;
        }
        .part-card .part-info .part-code {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10px;
            color: #1D4ED8;
            margin-top: 3px;
        }

        /* ── JUMLAH & SALDO ROW ── */
        .qty-saldo-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }
        .qty-box {
            background: #111827;
            color: #fff;
            border-radius: 6px;
            padding: 12px 14px;
            text-align: center;
        }
        .qty-box .box-label {
            font-size: 9px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0.6;
            margin-bottom: 4px;
        }
        .qty-box .box-number {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
        }
        .qty-box .box-unit {
            font-size: 10px;
            opacity: 0.5;
            margin-top: 3px;
        }
        .saldo-box {
            background: #F0FDF4;
            border: 1.5px solid #86EFAC;
            border-radius: 6px;
            padding: 12px 14px;
            text-align: center;
        }
        .saldo-box .box-label {
            font-size: 9px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6B7280;
            margin-bottom: 4px;
        }
        .saldo-box .box-number {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 32px;
            font-weight: 700;
            color: #16A34A;
            line-height: 1;
        }
        .saldo-box .box-unit {
            font-size: 10px;
            color: #86EFAC;
            margin-top: 3px;
        }

        /* ── TANDA TANGAN ── */
        .ttd-section {
            border-top: 1px dashed #D1D5DB;
            padding-top: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .ttd-box {
            text-align: center;
        }
        .ttd-box .ttd-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            margin-bottom: 36px;
        }
        .ttd-box .ttd-line {
            border-top: 1px solid #374151;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 600;
            color: #374151;
        }

        /* ── FOOTER NOTE ── */
        .footer-note {
            margin-top: 14px;
            text-align: center;
            font-size: 9px;
            color: #D1D5DB;
            letter-spacing: 0.04em;
        }

        /* ── TOMBOL LAYAR ── */
        .action-bar {
            width: 148mm;
            margin: 14px auto 0;
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'IBM Plex Sans', sans-serif;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-print { background: #1D4ED8; color: #fff; }
        .btn-close  { background: #E5E7EB; color: #374151; }

        /* ── PRINT ── */
        @media print {
            body { background: none; padding: 0; }
            .page { box-shadow: none; }
            .action-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="company">Gudang Sparepart</div>
            <div class="rayon">{{ $loan->nama_rayon }}</div>
        </div>
        <div class="header-right">
            <div class="doc-badge">Bukti Pinjam Part</div>
            <div class="no-transaksi">#{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- INFO TRANSAKSI --}}
    <div class="info-grid">
        <div class="info-item">
            <div class="label">Tanggal</div>
            <div class="value mono">{{ \Carbon\Carbon::parse($loan->created_at)->format('d/m/Y') }}</div>
        </div>
        <div class="info-item">
            <div class="label">Jam</div>
            <div class="value mono">{{ \Carbon\Carbon::parse($loan->created_at)->format('H:i') }} WIB</div>
        </div>
        <div class="info-item" style="grid-column: span 2;">
            <div class="label">Nama Teknisi</div>
            <div class="value">{{ $loan->nama_technician }}</div>
        </div>
    </div>

    {{-- DETAIL PART --}}
    <div class="section-label">Detail Sparepart</div>
    <div class="part-card">
        <div class="part-info">
            <div class="part-name">{{ $loan->nama_sparepart }}</div>
            <div class="part-code">{{ $loan->code_part ?? 'No Code' }}</div>
        </div>
    </div>

    {{-- JUMLAH & SALDO --}}
    <div class="qty-saldo-row">
        <div class="qty-box">
            <div class="box-label">Jumlah Dipinjam</div>
            <div class="box-number">{{ $loan->jumlah }}</div>
            <div class="box-unit">PCS</div>
        </div>
        <div class="saldo-box">
            <div class="box-label">Sisa di Tas Teknisi</div>
            <div class="box-number">{{ $sisaSaldo }}</div>
            <div class="box-unit">PCS</div>
        </div>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="ttd-label">Petugas Gudang</div>
            <div class="ttd-line">( ........................ )</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-label">Penerima / Teknisi</div>
            <div class="ttd-line">( {{ $loan->nama_technician }} )</div>
        </div>
    </div>

    <div class="footer-note">
        Dicetak otomatis oleh sistem · {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

</div>

{{-- TOMBOL AKSI (hanya di layar) --}}
<div class="action-bar">
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
    <button class="btn btn-close" onclick="window.close()">✕ Tutup</button>
</div>

<script>
    // Auto print langsung saat halaman terbuka
    window.addEventListener('load', function () {
        setTimeout(() => window.print(), 500);
    });
</script>

</body>
</html>