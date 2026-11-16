<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: #fff;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #111;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Kertas A5 landscape: 210mm x 148mm, padding 6mm => kotak luar 136mm */
        .sheet {
            width: 210mm;
            height: 148mm;
            padding: 6mm;
        }

        .outer {
            width: 100%;
            height: 136mm;
            border: 2px solid #000;
        }

        /*
         * ZONA TINGGI TETAP
         * Total: 17 + 8 + 7 + 50 + 22 + 30 = 134mm
         * Tinggi dalam border (134.9mm) sehingga tidak ada zona yang melampaui kotak.
         */

        .zone-header {
            height: 17mm;
            display: flex;
            border-bottom: 1.2px solid #000;
        }

        .header-logo {
            width: 24mm;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #000;
            font-weight: 900;
            font-size: 12px;
            letter-spacing: -0.5px;
            color: #0D47A1;
        }

        .header-company {
            flex: 1;
            padding: 0 4mm;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .company-name {
            font-weight: 800;
            font-size: 13px;
            line-height: 1.2;
        }

        .company-addr {
            font-size: 8.5px;
            font-weight: 500;
            color: #222;
            line-height: 1.3;
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

        .zone-judul {
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1.2px solid #000;
            font-size: 13px;
            font-weight: 700;
            text-decoration: underline;
            letter-spacing: 0.4px;
        }

        .zone-kepada {
            height: 7mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 4mm;
            border-bottom: 1.2px solid #000;
            font-size: 10px;
        }

        /* Tabel: 1 header (4.8mm) + 9 baris body (4.8mm) = 48mm, zona 50mm */
        .zone-tabel {
            height: 50mm;
            border-bottom: 1.2px solid #000;
        }

        .uraian-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .uraian-table th,
        .uraian-table td {
            height: 4.8mm;
            padding: 0 2.5mm;
            line-height: 4.8mm;
            font-size: 10px;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            border-right: 1px solid #000;
        }

        .uraian-table th:last-child,
        .uraian-table td:last-child {
            border-right: none;
        }

        .uraian-table thead th {
            font-weight: 700;
            text-align: center;
            background: #f3f3f3;
            border-bottom: 1.2px solid #000;
        }

        .uraian-table tbody tr:not(:last-child) td {
            border-bottom: 1px solid #0f0f0f;
        }

        .uraian-table tbody tr.fixed-row td {
            font-weight: 600;
            text-transform: uppercase;
        }

        .col-uraian {
            text-align: left;
            text-transform: uppercase;
        }

        .col-kode {
            width: 38mm;
            text-align: center;
        }

        .col-jumlah {
            width: 32mm;
            text-align: right;
        }

        /* Terbilang: 22mm */
        .zone-terbilang {
            height: 22mm;
            display: flex;
            justify-content: space-between;
            padding: 2mm 4mm;
            border-bottom: 1.2px solid #000;
            gap: 6mm;
        }

        .terbilang-left {
            flex: 1;
            font-size: 10px;
            line-height: 1.2;
        }

        .tb-line {
            display: flex;
            align-items: baseline;
            gap: 5px;
            margin-bottom: 1.2mm;
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

        .bayar-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 0.6mm;
        }

        .bayar-lbl {
            min-width: 30mm;
        }

        .check {
            font-size: 14px;
            font-weight: bold;
            line-height: 1;
            margin-left: 2px;
        }

        .terbilang-right {
            width: 52mm;
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
            margin-bottom: 1.2mm;
        }

        .bayar-right-val {
            border-bottom: 1px solid #000;
            width: 22mm;
            height: 3mm;
        }

        .jumlah-row {
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 5px;
            font-weight: 700;
            font-size: 11.5px;
        }

        .jumlah-val {
            border-bottom: 2.2px double #000;
            min-width: 24mm;
            text-align: right;
            padding-bottom: 0.5mm;
        }

        /* TTD: 30mm */
        .zone-ttd {
            height: 30mm;
            display: flex;
        }

        .ttd-cell {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 1.5mm 2mm 1.5mm;
            text-align: center;
            font-size: 10px;
            border-right: 1px solid #000;
        }

        .ttd-cell:last-child {
            border-right: none;
        }

        .ttd-name {
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            max-width: 100%;
        }

        /* Tombol cetak */
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
    <div class="no-print-container">
        <button class="btn-print" onclick="window.print()">
            🖨️ CETAK BUKTI
        </button>
    </div>

    @php
        // Kapasitas: 9 baris body = 3 baris tetap + 6 slot item
        $bodyRows = 9;
        $fixedRows = 3;
        $itemSlots = $bodyRows - $fixedRows;
        $emptyRows = max(0, $itemSlots - count($items));
    @endphp

    <div class="sheet">
        <div class="outer">

            {{-- HEADER --}}
            <div class="zone-header">
                <div class="header-logo">PT DGG</div>
                <div class="header-company">
                    <div class="company-name">PT. DINAMIKA GLOBAL GEMILANG</div>
                    <div class="company-addr">JL. GURAME NO. 20 KOTA BANDUNG 40262</div>
                    <div class="company-addr">JL. PULASAREN NO.56B PEKALIPAN KOTA CIREBON 45116</div>
                </div>
                <div class="header-novoucher">{{ $noVoucher }}</div>
            </div>

            {{-- JUDUL --}}
            <div class="zone-judul">BUKTI PENGELUARAN KAS/BANK</div>

            {{-- DIBAYAR KEPADA --}}
            <div class="zone-kepada">
                <span>Dibayar Kepada :&nbsp;
                    {{ $data->dibayar_kepada ?? '……………………………………………………………………………' }}</span>
                <span>{{ $tanggalStr }}</span>
            </div>

            {{-- TABEL URAIAN --}}
            <div class="zone-tabel">
                <table class="uraian-table">
                    <colgroup>
                        <col>
                        <col style="width: 38mm;">
                        <col style="width: 32mm;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-uraian">U r a i a n</th>
                            <th class="col-kode">Kode perkiraan</th>
                            <th class="col-jumlah">Jumlah (Rp.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="col-uraian">{{ strtoupper($item->uraian ?? '') }}</td>
                                <td class="col-kode">{{ $item->kode_perkiraan ?? '' }}</td>
                                <td class="col-jumlah">
                                    {{ $item->jumlah ? number_format($item->jumlah, 0, ',', '.') : '' }}</td>
                            </tr>
                        @endforeach

                        @for ($i = 0; $i < $emptyRows; $i++)
                            <tr>
                                <td class="col-uraian">&nbsp;</td>
                                <td class="col-kode"></td>
                                <td class="col-jumlah"></td>
                            </tr>
                        @endfor

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
            <div class="zone-terbilang">
                <div class="terbilang-left">
                    <div class="tb-line">
                        <span class="tb-label">Terbilang</span>
                        <span>:</span>
                        <span class="tb-val">{{ $terbilang }}</span>
                    </div>

                    <div>Dibayar dengan;</div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Uang Tunai</span>
                        <span>:</span>
                        <span class="check">
                            {{ ($data->jenis_pembayaran ?? 'Tunai') === 'Tunai' ? '✓' : '' }}
                        </span>
                    </div>
                    <div class="bayar-item">
                        <span class="bayar-lbl">- Cek/giro bilyet Bank</span>
                        <span>:</span>
                        <span class="check">
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

            {{-- TANDA TANGAN --}}
            <div class="zone-ttd">
                <div class="ttd-cell">
                    <div>Dibuat Oleh,</div>
                    <div class="ttd-name">( {{ $data->pembuat ?? '....................' }} )</div>
                </div>
                <div class="ttd-cell">
                    <div>Diketahui Oleh,</div>
                    <div class="ttd-name">( {{ $data->pemeriksa ?? '....................' }} )</div>
                </div>
                <div class="ttd-cell">
                    <div>Disetujui,</div>
                    <div class="ttd-name">( .................... )</div>
                </div>
                <div class="ttd-cell">
                    <div>Penerima,</div>
                    <div class="ttd-name">( {{ $data->penerima ?? '....................' }} )</div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
