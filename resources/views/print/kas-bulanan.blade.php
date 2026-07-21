<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .page {
            width: 100%;
            padding: 0;
        }

        /* ===== HEADER / KOP SURAT ===== */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .header .company {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        .header .periode {
            font-size: 11px;
            margin-top: 3px;
            font-style: italic;
        }

        /* ===== TABEL ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
            border: 1px solid #000;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: top;
        }

        th {
            background-color: #e9e9e9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .saldo-awal td {
            background: #fdf6e3;
            font-style: italic;
        }

        .total-row td {
            background: #e9e9e9;
            font-weight: bold;
        }

        .saldo-akhir td {
            background: #dcece1;
            font-weight: bold;
        }

        /* ===== FOOTER / TANDA TANGAN ===== */
        .footer {
            margin-top: 40px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .footer .keterangan-footer {
            font-size: 10px;
            width: 45%;
        }

        .footer .keterangan-footer p {
            margin-bottom: 4px;
        }

        .footer .ttd-block {
            width: 45%;
            text-align: center;
            font-size: 11px;
        }

        .footer .ttd-block .tanggal {
            margin-bottom: 4px;
        }

        .footer .ttd-block .jabatan {
            margin-bottom: 55px;
        }

        .footer .ttd-block .ttd-name {
            display: inline-block;
            min-width: 180px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
            text-decoration: underline;
        }

        .page-footer-note {
            margin-top: 25px;
            font-size: 9px;
            text-align: center;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }

        @page {
            size: A4 portrait;
            margin: 18mm 14mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        {{-- HEADER --}}
        <div class="header">
            <div class="company">PT Dinamika Global Gemilang</div>
            <div class="doc-title">Buku Kas Depo Cirebon</div>
            <div class="periode">Periode: {{ $namaBulan }} {{ $tahun }}</div>
        </div>

        {{-- TABEL --}}
        <table>
            <thead>
                <tr>
                    <th style="width:25px">No.</th>
                    <th style="width:60px">Tanggal</th>
                    <th style="width:65px">No. Surat</th>
                    <th>Keterangan</th>
                    <th style="width:80px">Uang Masuk</th>
                    <th style="width:80px">Uang Keluar</th>
                    <th style="width:85px">Sisa Saldo</th>
                </tr>
            </thead>
            <tbody>
                {{-- SALDO AWAL --}}
                <tr class="saldo-awal">
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td><em>Saldo Awal Bulan {{ $namaBulan }}</em></td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>

                {{-- DATA TRANSAKSI --}}
                @forelse ($rows as $row)
                    <tr>
                        {{-- REVISI UTAMA: Menggunakan $loop->iteration agar nomor urut pasti rapi 1, 2, 3... --}}
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $row['no_surat'] ?? '-' }}</td>

                        {{-- KOLOM KETERANGAN (UPPERCASE & DIPISAH KOMA) --}}
                        <td>
                            @if (!empty($row['uraian_koma']))
                                {{ strtoupper($row['uraian_koma']) }}
                            @elseif(!empty($row['uraian']))
                                {{ strtoupper($row['uraian']) }}
                            @else
                                {{ strtoupper($row['keterangan'] ?? '-') }}
                            @endif
                        </td>
                        <td class="text-right">
                            {{ $row['uang_masuk'] > 0 ? 'Rp ' . number_format($row['uang_masuk'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right">
                            {{ $row['uang_keluar'] > 0 ? 'Rp ' . number_format($row['uang_keluar'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right">Rp {{ number_format($row['sisa_saldo'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding:20px; color:#888;">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse

                {{-- TOTAL --}}
                @if ($rows->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL</td>
                        <td class="text-right">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr class="saldo-akhir">
                        <td colspan="6" class="text-right">SALDO AKHIR BULAN {{ strtoupper($namaBulan) }}</td>
                        <td class="text-right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="keterangan-footer">
                <p><strong>Keterangan:</strong></p>
                <p>Dokumen ini dicetak otomatis oleh DGG System</p>
                <p>dan merupakan catatan resmi kas umum periode berjalan.</p>
            </div>
            <div class="ttd-block">
                <p class="tanggal">Cirebon,
                    {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p class="jabatan">Admin,</p>
                <p class="ttd-name">&nbsp;</p>
            </div>
        </div>

        <div class="page-footer-note">
            Dicetak melalui DGG System &mdash; {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
        </div>

        {{-- TOMBOL CETAK --}}
        <div class="no-print" style="margin-top:20px; text-align:center;">
            <button onclick="window.print()"
                style="padding:10px 30px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()"
                style="margin-left:10px; padding:10px 20px; background:#6b7280; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">
                Tutup
            </button>
        </div>
    </div>
</body>

</html>
