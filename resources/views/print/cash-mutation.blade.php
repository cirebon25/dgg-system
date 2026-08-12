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

        .sheet {
            width: 210mm;
            height: 148mm;
            padding: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .outer {
            border: 2px solid #000;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header-row {
            display: flex;
            border-bottom: 1.2px solid #000;
            flex: 0 0 auto;
        }

        .header-logo {
            width: 24mm;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #000;
            padding: 3mm 0;
            font-weight: 900;
            font-size: 12px;
            letter-spacing: -0.5px;
            color: #0D47A1;
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

        .kepada-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.2px solid #000;
            padding: 2mm 4mm;
            font-size: 10px;
            flex: 0 0 auto;
        }

        .uraian-wrap {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            border-bottom: 1.2px solid #000;
            overflow: visible;
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
            border-bottom: 1px solid #0f0f0f;
        }

        .uraian-table tbody tr.fixed-row td {
            font-weight: 600;
            color: #0a0a0a;
            text-transform: uppercase;
        }

        .uraian-table tbody tr.empty-row td {
            height: 4.5mm;
        }

        .col-uraian {
            width: auto;
            text-align: left;
            text-transform: uppercase;
        }

        .col-kode {
            width: 38mm;
            text-align: center !important;
        }

        .col-jumlah {
            width: 32mm;
            text-align: right !important;
        }

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

        .tb-line {
            display: flex;
            align-items: baseline;
            margin-bottom: 2.5mm;
            gap: 5px;
        }

        .tb-label {
            font-weight: 700;
            min-width: 30mm;
            font-size: 12px;
        }

        .tb-val {
            font-weight: 700;
            font-style: italic;
            font-size: 12px;
            text-transform: uppercase;
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

        .ttd-row {
            display: flex;
            flex: 0 0 auto;
            height: 28mm;
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
            margin-top: 8.5mm;
            font-weight: 700;
            padding-bottom: 1.5mm;
        }

        /* Tombol Cetak / Print Styling & Sembunyikan saat cetak */
        .no-print-container {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
        }

        .btn-print {
            background-color: #0D47A1;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-print:hover {
            background-color: #1565C0;
        }

        @media print {
            .no-print-container {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Tombol Print Manual -->
    <div class="no-print-container">
        <button class="btn-print" onclick="window.print()">
            🖨️ CETAK BUKTI
        </button>
    </div>

    <div class="sheet">
        <div class="outer">

            {{-- HEADER --}}
            <div class="header-row">
                <div class="header-logo">
                    PT DGG
                </div>
                <div class="header-company">
                    <div class="company-name">PT. DINAMIKA GLOBAL GEMILANG</div>
                    <div class="company-addr">JL. GURAME NO. 20 KOTA BANDUNG 40262</div>
                    <div class="company-addr">JL. PULASAREN NO.56B PEKALIPAN KOTA CIREBON 45116</div>
                </div>
                <div class="header-novoucher">{{ $noVoucher }}</div>
            </div>

            {{-- JUDUL --}}
            <div class="judul-row">BUKTI PENGELUARAN KAS/BANK</div>

            {{-- DIBAYAR KEPADA --}}
            <div class="kepada-row">
                <span>Dibayar Kepada :&nbsp;
                    {{ $data->dibayar_kepada ?? '……………………………………………………………………………' }}</span>
                <span>{{ $tanggalStr }}</span>
            </div>

            {{-- TABEL URAIAN --}}
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
                        {{-- Baris data uraian --}}
                        @foreach ($items as $item)
                            <tr>
                                <td class="col-uraian">{{ strtoupper($item->uraian ?? '') }}</td>
                                <td class="col-kode">{{ $item->kode_perkiraan ?? '' }}</td>
                                <td class="col-jumlah">
                                    {{ $item->jumlah ? number_format($item->jumlah, 0, ',', '.') : '' }}</td>
                            </tr>
                        @endforeach

                        {{-- Baris kosong pengisi, supaya tabel tetap presisi 1 halaman --}}
                        @for ($i = 0; $i < $emptyRows; $i++)
                            <tr class="empty-row">
                                <td class="col-uraian">&nbsp;</td>
                                <td class="col-kode"></td>
                                <td class="col-jumlah"></td>
                            </tr>
                        @endfor

                        {{-- Baris tetap: PLAT / KM AWAL / KM AKHIR --}}
                        <tr class="fixed-row">
                            <td class="col-uraian">PLAT : {{ $vehiclePlat ?? '' }}</td>
                            <td class="col-kode"></td>
                            <td class="col-jumlah"></td>
                        </tr>
                        <tr class="fixed-row">
                            <td class="col-uraian">KM AWAL :
                                {{ isset($vehicleKmAwal) ? number_format($vehicleKmAwal, 0, ',', '.') : '' }}</td>
                            <td class="col-kode"></td>
                            <td class="col-jumlah"></td>
                        </tr>
                        <tr class="fixed-row">
                            <td class="col-uraian">KM AKHIR :
                                {{ isset($vehicleKmAkhir) ? number_format($vehicleKmAkhir, 0, ',', '.') : '' }}</td>
                            <td class="col-kode"></td>
                            <td class="col-jumlah"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- TERBILANG + DIBAYAR DENGAN --}}
            <div class="terbilang-block">
                <div class="terbilang-left">
                    <div class="tb-line">
                        <span class="tb-label">Terbilang</span>
                        <span>:</span>
                        <span class="tb-val" style="margin-left: 2px;">{{ $terbilang }}</span>
                    </div>

                    <div class="bayar-dengan-label">Dibayar dengan;</div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Uang Tunai</span>
                        <span>:</span>
                        <span style="font-size:14px; font-weight:bold; line-height:1; margin-left: 2px;">
                            {{ ($data->jenis_pembayaran ?? 'Tunai') === 'Tunai' ? '✓' : '' }}
                        </span>
                    </div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Cek/giro bilyet Bank</span>
                        <span>:</span>
                        <span style="font-size:14px; font-weight:bold; line-height:1; margin-left: 2px;">
                            {{ ($data->jenis_pembayaran ?? 'Tunai') === 'Cek/Giro' ? '✓' : '' }}
                        </span>
                    </div>
                </div>

                <div class="terbilang-right">
                    <div class="bayar-right-row">
                        <span>Rp.</span>
                        <span class="bayar-right-val"></span>
                    </div>
                    <div class="bayar-right-row">
                        <span>No. {{ $data->no_cek_giro ?? '_______' }}</span>
                        <span>Rp.</span>
                        <span class="bayar-right-val"></span>
                    </div>
                    <div class="jumlah-row">
                        <span>Jumlah Rp.</span>
                        <span class="jumlah-val">{{ $total > 0 ? number_format($total, 0, ',', '.') : '0' }}</span>
                    </div>
                </div>
            </div>

            {{-- TTD --}}
            <div class="ttd-row">
                <div class="ttd-cell">Dibuat Oleh,
                    <div class="ttd-name">( {{ $data->pembuat ?? '....................' }} )</div>
                </div>
                <div class="ttd-cell">Diketahui Oleh,
                    <div class="ttd-name">( {{ $data->pemeriksa ?? '....................' }} )</div>
                </div>
                <div class="ttd-cell">Disetujui,
                    <div class="ttd-name">( .................... )</div>
                </div>
                <div class="ttd-cell">Penerima,
                    <div class="ttd-name">( {{ $data->penerima ?? '....................' }} )</div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
