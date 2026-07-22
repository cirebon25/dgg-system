<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Penarikan Mesin #{{ str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* 🌟 MANDAT KERTAS MUTLAK: A5 LANDSCAPE */
        @page {
            size: A5 landscape;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #e5e5e5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            font-size: 11px;
            color: #1a1a1a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* 🌟 PEMBATAS KONTEN KERTAS A5 LANDSCAPE (210mm x 148mm) */
        .page {
            width: 210mm;
            height: 148mm;
            background: #fff;
            padding: 10mm 14mm 8mm;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 1. KOP PERUSAHAAN CLEAN & TEGAS */
        .kop {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .company-name {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #000;
        }

        .company-sub {
            font-size: 9.5px;
            color: #555;
            font-weight: 500;
            margin-top: 1px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #000;
        }

        .doc-no {
            font-size: 10px;
            color: #444;
            font-weight: 600;
            margin-top: 2px;
        }

        /* 2. AREA TANGGAL & DETAIL PELANGGAN */
        .customer-info-section {
            margin: 12px 0 6px 0;
        }

        .info-block-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-block-table td {
            padding: 3px 2px;
            vertical-align: top;
            font-size: 11px;
        }

        .info-block-table td.lbl {
            width: 120px;
            color: #555;
            font-weight: 500;
        }

        .info-block-table td.sym {
            width: 15px;
            text-align: center;
            color: #555;
        }

        .info-block-table td.val {
            font-weight: 700;
            color: #000;
        }

        /* 3. TABEL CLEAN MINIMALIS (TANPA BG HITAM) */
        .main-item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            border: 1.5px solid #000;
        }

        /* ❌ BACKGROUND HITAM DIHILANGKAN - BERUBAH JADI BERSIH PUTIH */
        .main-item-table th {
            background: #ffffff;
            color: #000000;
            border: 1.5px solid #000;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .main-item-table th.center,
        .main-item-table td.center {
            text-align: center;
        }

        .main-item-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 11px;
            vertical-align: middle;
            color: #000;
        }

        /* Mengunci tinggi 6 baris kosong agar presisi dan pas di kertas */
        .main-item-table td.row-kosong {
            height: 22px;
        }

        .txt-model {
            font-weight: 700;
            font-size: 11.5px;
        }

        .txt-sn {
            font-family: monospace;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .txt-kondisi {
            font-weight: 700;
            font-size: 9.5px;
            color: #444;
            display: inline-block;
            text-transform: uppercase;
        }

        /* 4. AREA TANDA TANGAN LEGA PAS DI BAWAH */
        .ttd-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }

        .ttd-box {
            text-align: center;
        }

        .ttd-box .ttd-lbl {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #444;
            font-weight: 600;
            margin-bottom: 32px;
        }

        .ttd-line {
            display: inline-block;
            width: 80%;
            border-top: 1.5px solid #000;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 700;
        }

        /* FOOTER CATATAN KAKI */
        .catatan {
            margin-top: 4px;
            font-size: 8px;
            color: #666;
            text-align: center;
            font-weight: 500;
        }

        /* BUTTON BAR UNTUK MONITOR */
        .action-bar {
            width: 210mm;
            margin: 10px auto 0;
            display: flex;
            gap: 8px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border: 1.5px solid #000;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            background: #fff;
            color: #000;
        }

        .btn-print {
            background: #000;
            color: #fff;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .page {
                border: none;
                box-shadow: none;
            }

            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="page">
        <div>
            {{-- KOP UTAMA --}}
            <div class="kop">
                <div class="kop-left">
                    <div class="company-name">PT Dinamika Global Gemilang</div>
                    <div class="company-sub">Jasa Sewa & Servis Mesin Fotokopi</div>
                </div>
                <div class="kop-right">
                    <div class="doc-title">Surat Penarikan Unit</div>
                    <div class="doc-no">
                        No:
                        SPU-{{ str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) }}/DGG/{{ \Carbon\Carbon::parse($withdrawal->tanggal_tarik)->format('m/Y') }}
                    </div>
                </div>
            </div>

            {{-- DATA DETAIL PELANGGAN --}}
            <div class="customer-info-section">
                <table class="info-block-table">
                    <tr>
                        <td class="lbl">Tanggal Penarikan</td>
                        <td class="sym">:</td>
                        <td class="val">
                            {{ \Carbon\Carbon::parse($withdrawal->tanggal_tarik)->locale('id')->isoFormat('D MMMM Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Nama Customer</td>
                        <td class="sym">:</td>
                        <td class="val">{{ $withdrawal->customer?->nama_customer ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Alamat Customer</td>
                        <td class="sym">:</td>
                        <td class="val">{{ $withdrawal->customer?->alamat ?? '-' }},
                            {{ $withdrawal->customer?->kota ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- TABEL INTI BERSIH (PUTIH SINKRON) DENGAN TOTAL 6 BARIS KOSONG --}}
            <table class="main-item-table">
                <thead>
                    <tr>
                        <th class="center" style="width: 50px;">NO</th>
                        <th style="width: 200px;">TIPE MODEL MESIN</th>
                        <th style="width: 180px;">NO SERI (SN)</th>
                        <th>KETERANGAN / ALASAN PENARIKAN</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 🌟 BARIS 1: DATA RIIL DATABASE --}}
                    <tr>
                        <td class="center" style="font-weight: 700;">1</td>
                        <td class="txt-model">
                            {{ $withdrawal->machine?->brand ?? 'CANON' }} {{ $withdrawal->machine?->tipe_model ?? '-' }}
                        </td>
                        <td class="txt-sn">{{ $withdrawal->machine?->serial_number ?? '-' }}</td>
                        <td>
                            <div style="line-height: 1.3; font-weight: 600;">{{ $withdrawal->alasan_penarikan }}</div>
                            <span class="txt-kondisi">
                                [ Kondisi: Kategori {{ $withdrawal->kondisi_akhir }} ]
                            </span>
                        </td>
                    </tr>

                    {{-- 🌟 BARIS 2 KOSONG --}}
                    <tr>
                        <td class="center row-kosong">2</td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                    </tr>

                    {{-- 🌟 BARIS 3 KOSONG --}}
                    <tr>
                        <td class="center row-kosong">3</td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                    </tr>

                    {{-- 🌟 BARIS 4 KOSONG --}}
                    <tr>
                        <td class="center row-kosong">4</td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                    </tr>

                    {{-- 🌟 BARIS 5 KOSONG --}}
                    <tr>
                        <td class="center row-kosong">5</td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                    </tr>

                    {{-- 🌟 BARIS 6 KOSONG --}}
                    <tr>
                        <td class="center row-kosong">6</td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                        <td class="row-kosong"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- AREA TANDA TANGAN & FOOTER --}}
        <div>
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="ttd-lbl">Petugas Lapangan</div>
                    <div class="ttd-line"></div>
                    <div style="font-size: 10px; font-weight: 600; margin-top: 4px;">( Teknisi )</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-lbl">PT DGG Cirebon</div>
                    <div class="ttd-line"></div>
                    <div style="font-size: 10px; font-weight: 600; margin-top: 4px;">( Pimpinan / Management )</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-lbl">Perwakilan Pelanggan</div>
                    <div class="ttd-line"></div>
                    <div style="font-size: 10px; font-weight: 600; margin-top: 4px;">(
                        {{ $withdrawal->customer?->nama_customer ?? 'Customer' }} )</div>
                </div>
            </div>

            <div class="catatan">
                Surat ini adalah bukti serah terima pemindahan aset unit mesin fotokopi sah PT DGG. Harap disimpan
                dengan baik.<br>
                Sistem DGG &copy; {{ date('Y') }} — Dokumen administrasi resmi dicetak otomatis oleh sistem.
            </div>
        </div>
    </div>

    {{-- TOMBOL DI LAYAR --}}
    <div class="action-bar">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Nota Sempurna (A5 Landscape)</button>
        <button class="btn" onclick="window.location.href='/admin/machine-withdrawals'">← Kembali ke List</button>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(() => window.print(), 600);
        });
    </script>

</body>

</html>
