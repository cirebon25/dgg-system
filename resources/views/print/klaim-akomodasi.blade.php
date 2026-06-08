<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kalkulasi Akomodasi - {{ $claim->technician->nama_technician }}</title>
    <style>
        /* PAKSA UKURAN KERTAS A5 POTRET */
        @page {
            size: 148mm 210mm;
            margin: 0;
            /* Margin nol karena kita pakai padding wrapper */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 148mm;
            height: 210mm;
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            overflow: hidden;
        }

        .wrapper {
            width: 148mm;
            height: 210mm;
            padding: 8mm 9mm;
            /* Ruang tepi kertas */
            display: flex;
            flex-direction: column;
        }

        /* ======================================================== */
        /* KOTAK FRAME LUAR BESAR MENYATU (Sesuai Gambar Referensi) */
        /* ======================================================== */
        .outer-frame {
            width: 100%;
            height: 100%;
            border: 2px solid #000;
            /* Garis kotak luar tegas tidak putus */
            padding: 5mm 5mm;
            display: flex;
            flex-direction: column;
        }

        /* KOP SURAT */
        .kop {
            display: flex;
            align-items: center;
            margin-bottom: 4mm;
            flex-shrink: 0;
        }

        .logo-box {
            width: 15mm;
            height: 12mm;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 10px;
            margin-right: 4mm;
            flex-shrink: 0;
            line-height: 1.1;
        }

        .kop-title {
            flex: 1;
            text-align: center;
            font-size: 12px;
            font-weight: 900;
            text-decoration: underline;
            text-transform: uppercase;
            padding-right: 12mm;
            /* Penyeimbang logo */
        }

        /* MASTER TABLE UNTUK DATA & BIAYA (FORMAL) */
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            flex-shrink: 0;
        }

        .form-table td {
            padding: 2.5px 2px;
            font-size: 9px;
            vertical-align: middle;
        }

        .form-table td.lbl {
            width: 32%;
            font-weight: bold;
            text-transform: uppercase;
        }

        .form-table td.sym {
            width: 4%;
            text-align: center;
        }

        /* Dioptimalkan agar RP dan Nominal bergeser penuh ke kanan */
        .form-table td.eq {
            width: 44%;
            text-align: right;
            padding-right: 4px;
            font-weight: bold;
        }

        .form-table td.val {
            width: 20%;
            text-align: right;
            font-weight: bold;
        }

        .periode-text {
            font-weight: normal;
        }

        .periode-text b {
            margin-left: 10px;
        }

        /* FORMAT TOTAL AKUNTANSI FORMAL (BUKAN PUTUS-PUTUS) */
        .total-row td {
            font-weight: 900;
            padding: 5px 2px;
        }

        .total-row td.lbl-total,
        .total-row td.sym-total,
        .total-row td.eq-total,
        .total-row td.val-total {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        .total-row td.eq-total {
            text-align: right;
            padding-right: 4px;
        }

        .total-row td.val-total {
            text-align: right;
        }

        /* TERBILANG */
        .terbilang {
            font-size: 8.5px;
            margin-top: 2mm;
            margin-bottom: 1mm;
            flex-shrink: 0;
        }

        .terbilang strong {
            font-style: italic;
            text-transform: uppercase;
            margin-left: 5px;
        }

        .garis {
            border-top: 1px solid #000;
            margin: 2mm 0 1.5mm 0;
            flex-shrink: 0;
        }

        .sub-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1.5mm;
            flex-shrink: 0;
        }

        /* TABEL DAFTAR KUNJUNGAN FORMAL */
        .visit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 3mm;
            flex: 1;
        }

        .visit-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            background: #fff;
        }

        .visit-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            height: 18px;
            vertical-align: middle;
        }

        .visit-table td.center {
            text-align: center;
        }

        /* AREA TANDA TANGAN */
        .ttd-section {
            display: flex;
            justify-content: space-around;
            text-align: center;
            font-size: 8.5px;
            flex-shrink: 0;
            margin-top: 1mm;
            margin-bottom: 2mm;
        }

        .ttd-box {
            width: 30%;
        }

        .ttd-box .lbl {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12mm;
        }

        .ttd-box .line {
            display: inline-block;
            width: 85%;
            border-top: 1px solid #000;
            padding-top: 2px;
            font-weight: 700;
        }

        /* KOTAK DOUBLE BAGIAN BAWAH */
        .info-bottom {
            display: flex;
            border: 1px solid #000;
            font-size: 8.5px;
            flex-shrink: 0;
        }

        .info-bottom .col {
            flex: 1;
            padding: 3mm 4mm;
        }

        .info-bottom .col:first-child {
            border-right: 1px solid #000;
        }

        .info-bottom .col table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-bottom .col table td {
            padding: 1px 2px;
            line-height: 1.4;
        }

        .info-bottom .col table td.lbl {
            width: 60px;
            font-weight: bold;
        }

        .disetujui {
            text-align: right;
            font-weight: 900;
            margin-top: 4mm;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        /* UTILITY ACTION BAR BROWSER */
        .action-bar {
            margin-bottom: 10px;
            display: flex;
            gap: 6px;
        }

        .btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            border: 1.5px solid #000;
            border-radius: 4px;
            background: #fff;
        }

        .btn-print {
            background: #000;
            color: #fff;
        }

        @media print {
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar" style="padding: 10px; background: #f5f5f5; border-bottom: 1px solid #ddd;">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak (A5)</button>
        <button class="btn" onclick="window.location.href='/admin/accommodation-claims'">← Kembali</button>
    </div>

    <div class="wrapper">

        <div class="outer-frame">

            {{-- KOP --}}
            <div class="kop">
                <div class="logo-box">PT DGG</div>
                <div class="kop-title">Kalkulasi Pengeluaran Luar Kota</div>
            </div>

            {{-- MASTER DATA DAN RENCANA BIAYA --}}
            <table class="form-table">
                <tr>
                    <td class="lbl">WILAYAH</td>
                    <td class="sym">:</td>
                    <td colspan="2">{{ $claim->wilayah }}</td>
                </tr>
                <tr>
                    <td class="lbl">NAMA</td>
                    <td class="sym">:</td>
                    <td colspan="2">{{ $claim->technician->nama_technician }}</td>
                </tr>
                <tr>
                    <td class="lbl">DARI TANGGAL</td>
                    <td class="sym">:</td>
                    <td colspan="2" class="periode-text">
                        {{ \Carbon\Carbon::parse($claim->dari_tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                        <b>S/D TANGGAL :</b>
                        {{ \Carbon\Carbon::parse($claim->sampai_tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="height: 2mm;"></td>
                </tr>
                <tr>
                    <td class="lbl">BIAYA TRANSPORTASI</td>
                    <td class="sym">:</td>
                    <td class="eq">= RP.</td>
                    <td class="val">
                        {{ $claim->biaya_transportasi > 0 ? number_format($claim->biaya_transportasi, 0, ',', '.') : '' }}
                    </td>
                </tr>
                <tr>
                    <td class="lbl">KONSUMSI KARYAWAN</td>
                    <td class="sym">:</td>
                    <td class="eq">= RP.</td>
                    <td class="val">
                        {{ $claim->konsumsi_karyawan > 0 ? number_format($claim->konsumsi_karyawan, 0, ',', '.') : '' }}
                    </td>
                </tr>
                <tr>
                    <td class="lbl">{{ strtoupper($claim->keterangan_lain_1 ?: 'PENGELUARAN LAIN') }}</td>
                    <td class="sym">:</td>
                    <td class="eq">= RP.</td>
                    <td class="val">
                        {{ $claim->pengeluaran_lain_1 > 0 ? number_format($claim->pengeluaran_lain_1, 0, ',', '.') : '' }}
                    </td>
                </tr>
                @if ($claim->keterangan_lain_2 || $claim->pengeluaran_lain_2 > 0)
                    <tr>
                        <td class="lbl">{{ strtoupper($claim->keterangan_lain_2) }}</td>
                        <td class="sym">:</td>
                        <td class="eq">= RP.</td>
                        <td class="val">
                            {{ $claim->pengeluaran_lain_2 > 0 ? number_format($claim->pengeluaran_lain_2, 0, ',', '.') : '' }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="lbl"></td>
                        <td class="sym">:</td>
                        <td class="eq">= RP.</td>
                        <td class="val"></td>
                    </tr>
                @endif

                {{-- TOTAL TRANSAKSI AKUNTANSI (Sudah Diperbaiki Sejajar Kanan Pas) --}}
                <tr class="total-row">
                    <td class="lbl-total">TOTAL BIAYA PENGELUARAN</td>
                    <td class="sym-total">:</td>
                    <td class="eq-total">= RP.</td>
                    <td class="val-total">{{ number_format($claim->total_biaya, 0, ',', '.') }}</td>
                </tr>
            </table>

            {{-- TERBILANG --}}
            <div class="terbilang">
                TERBILANG :
                <strong>
                    @php
                        if (!function_exists('terbilang')) {
                            function terbilang($n)
                            {
                                $n = (int) abs($n);
                                $huruf = [
                                    '',
                                    'Satu',
                                    'Dua',
                                    'Tiga',
                                    'Empat',
                                    'Lima',
                                    'Enam',
                                    'Tujuh',
                                    'Delapan',
                                    'Sembilan',
                                    'Sepuluh',
                                    'Sebelas',
                                ];
                                if ($n < 12) {
                                    return $huruf[$n];
                                }
                                if ($n < 20) {
                                    return $huruf[$n - 10] . ' Belas';
                                }
                                if ($n < 100) {
                                    return $huruf[(int) ($n / 10)] . ' Puluh ' . terbilang($n % 10);
                                }
                                if ($n < 200) {
                                    return 'Seratus ' . terbilang($n - 100);
                                }
                                if ($n < 1000) {
                                    return $huruf[(int) ($n / 100)] . ' Ratus ' . terbilang($n % 100);
                                }
                                if ($n < 2000) {
                                    return 'Seribu ' . terbilang($n - 1000);
                                }
                                if ($n < 1000000) {
                                    return terbilang((int) ($n / 1000)) . ' Ribu ' . terbilang($n % 1000);
                                }
                                if ($n < 1000000000) {
                                    return terbilang((int) ($n / 1000000)) . ' Juta ' . terbilang($n % 1000000);
                                }
                                return terbilang((int) ($n / 1000000000)) . ' Miliar ' . terbilang($n % 1000000000);
                            }
                        }
                        echo trim(terbilang((int) $claim->total_biaya)) . ' Rupiah';
                    @endphp
                </strong>
            </div>

            <div class="garis"></div>
            <div class="sub-title">Daftar Kunjungan</div>

            {{-- TABEL KUNJUNGAN PAS 7 BARIS UTK JAGA LAYOUT --}}
            <table class="visit-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">NO</th>
                        <th style="width: 37%;">NAMA CUSTOMER</th>
                        <th style="width: 35%;">ALAMAT</th>
                        <th style="width: 20%;">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claim->visits as $visit)
                        <tr>
                            <td class="center">{{ $visit->no_urut }}</td>
                            <td>{{ $visit->customer?->nama_customer ?? $visit->nama_customer }}</td>
                            <td>{{ $visit->customer?->alamat ?? $visit->alamat }}</td>
                            <td class="center">{{ $visit->keterangan }}</td>
                        </tr>
                    @empty
                    @endforelse

                    @for ($i = $claim->visits->count(); $i < 7; $i++)
                        <tr>
                            <td class="center" style="color:#ccc;">{{ $i + 1 }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            {{-- AREA UNTUK PARAF / TANDA TANGAN --}}
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="lbl">Dibuat</div>
                    <div class="line"></div>
                </div>
                <div class="ttd-box">
                    <div class="lbl">Disetujui</div>
                    <div class="line"></div>
                </div>
                <div class="ttd-box">
                    <div class="lbl">Diketahui</div>
                    <div class="line"></div>
                </div>
            </div>

            {{-- BR UNTUK MENGEPAS SISA RUANG DI ATAS KOTAK KEMBAR BAWAH --}}
            <br>
            {{-- KOTAK INFORMASI BAWAH KEMBAR --}}
            <div class="info-bottom">
                <div class="col" style="border: 1px solid #000;">
                    <div style="text-align: center; font-weight: bold; padding: 5px; border-bottom: 1px solid #000;">
                        UANG MAKAN
                    </div>
                    <table>
                        <tr>
                            <td class="lbl">NAMA</td>
                            <td>: {{ $claim->technician->nama_technician }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">TUJUAN</td>
                            <td>: {{ $claim->wilayah }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">TANGGAL</td>
                            <td>: {{ \Carbon\Carbon::parse($claim->dari_tanggal)->format('d/m') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">LAMA</td>
                            <td>: {{ $claim->lama_hari }} HARI</td>
                        </tr>
                        <tr>
                            <td class="lbl">NOMINAL</td>
                            <td>: RP.{{ number_format($claim->konsumsi_karyawan, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                    <div class="disetujui">DISETUJUI</div>
                </div>
                <div class="col" style="border: 1px solid #000;">
                    <div style="text-align: center; font-weight: bold; padding: 5px; border-bottom: 1px solid #000;">
                        UANG MAKAN
                    </div>
                    <table>
                        <tr>
                            <td class="lbl">NAMA</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td class="lbl">TUJUAN</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td class="lbl">TANGGAL</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td class="lbl">LAMA</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td class="lbl">NOMINAL</td>
                              <td>:</td>
                        </tr>
                    </table>
                    <div class="disetujui">DISETUJUI</div>
                </div>
            </div>

        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(() => window.print(), 500);
        });
    </script>
</body>

</html>