<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }

        .page { width: 100%; padding: 20px 30px; }

        .header { text-align: center; margin-bottom: 16px; }
        .header h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .header h3 { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .header p  { font-size: 11px; margin-top: 2px; }

        .divider { border-top: 2px solid #000; margin: 8px 0 4px; }
        .divider-thin { border-top: 1px solid #000; margin: 4px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px 7px; vertical-align: middle; }
        th { background-color: #f0f0f0; text-align: center; font-size: 11px; font-weight: bold; }
        td { font-size: 11px; }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        .saldo-awal td { background: #fffbe6; font-style: italic; }
        .total-row td  { background: #f0f0f0; font-weight: bold; }
        .saldo-akhir td { background: #e6f4ea; font-weight: bold; }

        .footer { margin-top: 30px; display: flex; justify-content: flex-end; }
        .ttd { text-align: center; width: 200px; }
        .ttd .ttd-name { margin-top: 60px; border-top: 1px solid #000; padding-top: 4px; font-weight: bold; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <h2>DGG System</h2>
        <h3>Buku Kas Umum</h3>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
        <div class="divider"></div>
        <div class="divider-thin"></div>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th style="width:40px">No.</th>
                <th style="width:80px">Tanggal</th>
                <th style="width:90px">No. Surat</th>
                <th>Keterangan</th>
                <th style="width:110px">Uang Masuk</th>
                <th style="width:110px">Uang Keluar</th>
                <th style="width:120px">Sisa Saldo</th>
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
                <td class="text-center">{{ $row['no_urut'] }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                <td class="text-center">{{ $row['no_surat'] ?? '-' }}</td>
                <td>{{ $row['keterangan'] }}</td>
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
            @if($rows->isNotEmpty())
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

    {{-- FOOTER TTD --}}
    <div class="footer">
        <div class="ttd">
            <p>Bandung, {{ \Carbon\Carbon::create($tahun, $bulan)->endOfMonth()->isoFormat('D MMMM Y') }}</p>
            <div class="ttd-name">
                Bendahara
            </div>
        </div>
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