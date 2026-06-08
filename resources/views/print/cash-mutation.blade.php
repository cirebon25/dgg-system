<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengeluaran Kas/Bank</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; background: #fff; }

        /* OUTER */
        .outer { border: 2px solid #000; width: 100%; }

        /* HEADER */
        .header-row { display: flex; border-bottom: 1px solid #000; }
        .header-logo {
            width: 78px; min-width: 78px;
            display: flex; align-items: center; justify-content: center;
            border-right: 1px solid #000; padding: 4px;
        }
        .header-company {
            flex: 1; padding: 5px 8px;
            border-right: 1px solid #000;
            display: flex; flex-direction: column; justify-content: center;
        }
        .company-name { font-weight: bold; font-size: 12px; }
        .company-addr { font-size: 8.5px; line-height: 1.7; }
        .header-novoucher {
            width: 245px; min-width: 245px;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 11.5px; letter-spacing: 1px;
            padding: 4px 8px;
        }

        /* JUDUL */
        .judul-row {
            text-align: center; font-size: 13px; font-weight: bold;
            text-decoration: underline; letter-spacing: 0.5px;
            padding: 5px 8px; border-bottom: 1px solid #000;
        }

        /* DIBAYAR KEPADA */
        .kepada-row {
            display: flex; border-bottom: 1px solid #000; padding: 4px 8px;
        }
        .kepada-left { flex: 1; font-size: 10px; }
        .kepada-right { font-size: 10px; white-space: nowrap; padding-left: 10px; }

        /* TABEL URAIAN - semua border lengkap */
        .uraian-table { width: 100%; border-collapse: collapse; }

        .uraian-table thead tr th {
            border-top: none;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
            padding: 3px 7px;
            background: #f0f0f0;
            font-size: 10.5px;
            font-weight: bold;
            text-align: center;
        }
        .uraian-table thead tr th:first-child { border-left: none; }

        .uraian-table tbody tr td {
            border-top: none;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
            padding: 3px 7px;
            font-size: 10.5px;
        }
        .uraian-table tbody tr td:first-child { border-left: none; }

        .col-uraian { width: auto; }
        .col-kode   { width: 175px; text-align: center !important; }
        .col-jumlah { width: 130px; text-align: right !important; }
        .row-empty td { height: 19px; }

        .vehicle-detail { font-size: 8px; color: #555; font-style: italic; margin-top: 1px; }

        /* LAMPIRAN */
        .lampiran-row {
            font-style: italic; font-weight: bold; font-size: 9.5px;
            padding: 3px 8px; background: #fafafa;
            border-top: none; border-bottom: 1px solid #000;
        }

        /* FOOTER */
        .footer-row { display: flex; border-bottom: 1px solid #000; min-height: 90px; }
        .footer-left {
            flex: 1; padding: 7px 10px;
            border-right: 1px solid #000; font-size: 10px;
        }
        .terbilang-line { display: flex; gap: 6px; margin-bottom: 7px; align-items: flex-start; }
        .tb-label { font-weight: bold; min-width: 72px; }
        .tb-val   { font-weight: bold; font-style: italic; }
        .bayar-item { display: flex; align-items: center; gap: 8px; margin-top: 4px; font-size: 10px; }
        .bayar-lbl  { min-width: 130px; }

        .footer-right { width: 260px; min-width: 260px; padding: 6px 0 4px 0; font-size: 10px; }

        /* Baris Rp kosong */
        .rp-row { display: flex; align-items: flex-end; padding: 2px 8px 3px; }
        .rp-label { flex: 1; text-align: right; font-size: 9px; padding-right: 4px; }
        .rp-text  { width: 32px; text-align: right; padding-right: 4px; font-size: 10px; }
        .rp-val   {
            width: 115px; min-height: 16px;
            border-bottom: 1px solid #000;
            text-align: right; padding-right: 4px;
        }

        /* Baris Jumlah */
        .rp-row-total { display: flex; align-items: flex-end; padding: 6px 8px 3px; }
        .rp-total-label { flex: 1; text-align: right; font-weight: bold; font-size: 11px; padding-right: 4px; }
        .rp-total-text  { width: 32px; text-align: right; font-weight: bold; font-size: 11px; padding-right: 4px; }
        .rp-total-val   {
            width: 115px; text-align: right;
            border-bottom: 3px double #000;
            font-weight: bold; font-size: 12px;
            padding-right: 4px; padding-bottom: 2px;
        }

        /* TTD */
        .ttd-row { display: flex; }
        .ttd-cell {
            flex: 1; text-align: center;
            padding: 5px; height: 78px;
            display: flex; flex-direction: column; align-items: center;
            font-size: 10px;
            border-right: 1px solid #000;
        }
        .ttd-cell:last-child { border-right: none; }
        .ttd-name { margin-top: auto; font-weight: bold; padding-bottom: 4px; }
    </style>
</head>
<body>
<div class="outer">

    {{-- HEADER --}}
    <div class="header-row">
        <div class="header-logo">
            <svg width="58" height="58" viewBox="0 0 58 58" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="g1" cx="38%" cy="32%" r="70%">
                        <stop offset="0%"   stop-color="#64B5F6"/>
                        <stop offset="48%"  stop-color="#1565C0"/>
                        <stop offset="100%" stop-color="#0D47A1"/>
                    </radialGradient>
                </defs>
                <circle cx="29" cy="29" r="27.5" fill="url(#g1)" stroke="#888" stroke-width="1.2"/>
                <path d="M29,29 L53,42 A27.5,27.5 0 0,1 12,54 Z" fill="#B71C1C" opacity="0.88"/>
                <circle cx="29" cy="29" r="19" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
                <text x="29" y="34" text-anchor="middle" fill="#fff"
                      font-family="Arial Black,Arial" font-weight="900"
                      font-size="11.5" letter-spacing="-0.5">DGG</text>
            </svg>
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
        <div class="kepada-left">Dibayar Kepada :&nbsp; ............................................................................................................</div>
        <div class="kepada-right">{{ $tanggalStr }}</div>
    </div>

    {{-- TABEL URAIAN --}}
    <table class="uraian-table">
        <thead>
            <tr>
                <th class="col-uraian">U r a i a n</th>
                <th class="col-kode">Kode perkiraan</th>
                <th class="col-jumlah">Jumlah (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="col-uraian">
                    {{ strtoupper($item->uraian ?? '') }}
                    @if(!empty($item->plat_nomor) || !empty($item->km_awal) || !empty($item->km_akhir))
                        <div class="vehicle-detail">
                            [@if(!empty($item->plat_nomor)) Plat No: {{ strtoupper($item->plat_nomor) }} @endif
                             @if(!empty($item->km_awal)) | KM Awal: {{ number_format($item->km_awal,0,',','.') }} @endif
                             @if(!empty($item->km_akhir)) | KM Akhir: {{ number_format($item->km_akhir,0,',','.') }} @endif]
                        </div>
                    @endif
                </td>
                <td class="col-kode" style="text-align:center;">{{ $item->kode_perkiraan ?? '' }}</td>
                <td class="col-jumlah" style="text-align:right;">{{ $item->jumlah ? number_format($item->jumlah,0,',','.') : '' }}</td>
            </tr>
            @endforeach
            @for($i = 0; $i < $emptyRows; $i++)
            <tr class="row-empty">
                <td class="col-uraian" style="color:transparent;">-</td>
                <td class="col-kode"></td>
                <td class="col-jumlah"></td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- LAMPIRAN --}}
    <div class="lampiran-row">Lampirkan bukti-bukti pembayaran</div>

    {{-- FOOTER --}}
    <div class="footer-row">
        <div class="footer-left">
            <div class="terbilang-line">
                <span class="tb-label">Terbilang</span>
                <span>:</span>
                <span class="tb-val"># {{ $terbilang }} #</span>
            </div>
            <div style="font-size:10px;">Dibayar dengan;</div>
            <div class="bayar-item">
                <span class="bayar-lbl">- Uang Tunai</span>
                <span>:</span>
                <span style="font-size:16px;font-weight:bold;line-height:1;">✓</span>
            </div>
            <div class="bayar-item">
                <span class="bayar-lbl">- Cek/giro bilyet Bank</span>
                <span>:</span>
                <span style="border-bottom:1px solid #000;min-width:130px;display:inline-block;">&nbsp;</span>
            </div>
        </div>
        <div class="footer-right">
            <div class="rp-row">
                <span class="rp-label"></span>
                <span class="rp-text">Rp.</span>
                <span class="rp-val"></span>
            </div>
            <div class="rp-row">
                <span class="rp-label">No._________________</span>
                <span class="rp-text">Rp.</span>
                <span class="rp-val"></span>
            </div>
            <div class="rp-row-total">
                <span class="rp-total-label">Jumlah</span>
                <span class="rp-total-text">Rp.</span>
                <span class="rp-total-val">{{ $total > 0 ? number_format($total,0,',','.') : '0' }}</span>
            </div>
        </div>
    </div>

    {{-- TTD --}}
    <div class="ttd-row">
        <div class="ttd-cell">Dibuat Oleh,<div class="ttd-name">( {{ $data->pembuat ?? 'RUDI' }} )</div></div>
        <div class="ttd-cell">Diketahui Oleh,<div class="ttd-name">( {{ $data->pemeriksa ?? 'RIZEN' }} )</div></div>
        <div class="ttd-cell">Disetujui,<div class="ttd-name">( .................... )</div></div>
        <div class="ttd-cell">Penerima,<div class="ttd-name">( {{ $data->penerima ?? 'RUDI' }} )</div></div>
    </div>

</div>
<script>
    window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 400); });
</script>
</body>
</html>