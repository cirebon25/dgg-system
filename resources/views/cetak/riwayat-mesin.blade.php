@php
    $f = fn($v) => number_format((float) $v, 0, ',', '.');
    $adaTagihan = $kontrak && $kontrak->sewa !== null;

    // ===== Filter Part (dibaca dari ?part=...) =====
    $semuaGanti = collect($gantiTahun)->values();
    $daftarPart = $semuaGanti->pluck('part')->filter()->unique()->sort()->values();
    $partReq = request('part');
    $partDipilih = $daftarPart->contains($partReq) ? $partReq : null;

    // Selisih sudah dihitung di controller dari SELURUH riwayat,
    // jadi menyaring di sini tidak mengubah angka selisih.
    $gantiTampil = $partDipilih ? $semuaGanti->where('part', $partDipilih)->values() : $semuaGanti;
    $totalTampil = $partDipilih
        ? collect($totalPart)->where('nama', $partDipilih)->values()
        : collect($totalPart)->values();

    // Format selisih: +1.234 / ⚠ -50 / -
    $fmtSel = fn($v) => $v === null ? '-' : ($v < 0 ? '⚠ ' . $f($v) : '+' . $f($v));

    // Statistik untuk mode 1 part
    $selList = $gantiTampil->pluck('selisih_bw')->filter(fn($v) => $v !== null && $v >= 0)->values();
    $hariList = $gantiTampil->pluck('hari')->filter(fn($v) => $v !== null)->values();
    $maxSel = max(1, (float) ($selList->max() ?? 1));

    // ===== Grafik pemakaian per bulan =====
    // Baris kosong (tanpa kunjungan dan tanpa data) tidak ditampilkan di grafik.
    $barData = collect($deret)->reject(fn($r) => $r->kosong)->values();
    $bwMax = max(1, (float) ($barData->max(fn($r) => $r->bw) ?? 1));

    // Tertinggi dan terendah hanya dari bulan yang sudah terhitung penuh (bukan Parsial).
    $kandidat = $barData->reject(fn($r) => $r->status === 'Parsial')->values();
    $maxKey = null;
    $minKey = null;
    if ($kandidat->count() >= 2) {
        $maxKey = $kandidat->sortByDesc('bw')->first()->key;
        $minKey = $kandidat->sortBy('bw')->first()->key;
        if ($maxKey === $minKey) {
            $minKey = null; // semua bulan bernilai sama, tidak ada yang terendah
        }
    }

    // Kunci tetap untuk catatan keterangan per baris (tidak bergantung pada urutan tampil).
    $kunciKet = fn($r) => md5(implode('|', [$r->part, $r->tanggal->format('Y-m-d'), $r->qty, $r->bw]));
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Mesin {{ $machine->serial_number }} - {{ $periodeLabel }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .toolbar {
            margin-bottom: 12px;
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar button,
        .toolbar select {
            padding: 7px 14px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #333;
            background: #fff;
            border-radius: 4px;
        }

        .toolbar select#part {
            min-width: 260px;
            border-color: #1f4e79;
            border-width: 2px;
        }

        .toolbar label {
            font-size: 12px;
            font-weight: bold;
        }

        .judul {
            background: #1f4e79;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            padding: 7px 10px;
            display: flex;
            justify-content: space-between;
        }

        .sub {
            font-weight: bold;
            margin: 16px 0 5px;
            font-size: 12px;
            color: #1f4e79;
        }

        .sub small {
            font-weight: normal;
            color: #555;
            font-size: 10px;
        }

        table.info {
            border-collapse: collapse;
            margin: 8px 0 8px;
            width: 100%;
        }

        table.info th {
            background: #dbe5f1;
            border: 1px solid #8ea9c8;
            padding: 4px 8px;
            font-size: 10px;
            text-align: left;
            width: 18%;
        }

        table.info td {
            border: 1px solid #8ea9c8;
            padding: 4px 8px;
            font-weight: bold;
            width: 32%;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #1f4e79;
            color: #fff;
            padding: 5px 6px;
            font-size: 10px;
            text-align: center;
            border: 1px solid #1f4e79;
        }

        /* Penyejajaran header = isi kolom */
        table.data th.l {
            text-align: left;
        }

        table.data th.r {
            text-align: right;
        }

        table.data th.c {
            text-align: center;
        }

        table.data td {
            padding: 4px 6px;
            border-bottom: 1px solid #d9d9d9;
            vertical-align: top;
        }

        table.data td.angka {
            text-align: right;
        }

        table.data td.tebal {
            text-align: right;
            font-weight: bold;
            background: #f2f2f2;
        }

        table.data td.minus {
            color: #c00000;
            background: #fde9e9;
        }

        table.data tfoot td {
            background: #deeaf6;
            font-weight: bold;
            border-top: 2px solid #2e75b6;
        }

        table.data tfoot td.angka {
            text-align: right;
        }

        table.data tr.ini td {
            background: #fff2cc;
        }

        table.data tr.kosong td {
            color: #999;
        }

        /* Tagihan */
        table.data th.tagihan {
            background: #548235;
            border-color: #548235;
        }

        td.total-tagihan {
            text-align: right;
            font-weight: bold;
            background: #e2efda;
        }

        .box-ini {
            border: 2px solid #2e75b6;
            background: #ddebf7;
            padding: 7px 12px;
            margin: 6px 0 8px;
            font-size: 12px;
        }

        .box-ini .angka {
            font-size: 17px;
            font-weight: bold;
            color: #1f4e79;
        }

        .box-ini .ket {
            font-size: 10px;
            color: #444;
            margin-top: 2px;
        }

        .st-terukur {
            color: #375623;
            font-weight: bold;
        }

        .st-estimasi {
            color: #7f6000;
            font-weight: bold;
        }

        .st-parsial {
            color: #c55a11;
            font-weight: bold;
        }

        tr.siklus td {
            background: #fff2cc;
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #bf9000;
        }

        /* Penanda tanggal kembar */
        tr.tgl-0 td {
            background: #e2efda;
        }

        tr.tgl-1 td {
            background: #fff2cc;
        }

        tr.tgl-2 td {
            background: #ddebf7;
        }

        tr.tgl-3 td {
            background: #fce4d6;
        }

        tr.tgl-0 td:first-child {
            border-left: 5px solid #70ad47;
        }

        tr.tgl-1 td:first-child {
            border-left: 5px solid #bf9000;
        }

        tr.tgl-2 td:first-child {
            border-left: 5px solid #2e75b6;
        }

        tr.tgl-3 td:first-child {
            border-left: 5px solid #c55a11;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
            background: #e9e9e9;
        }

        .b-service {
            background: #ffe699;
        }

        .b-ganti_part {
            background: #f4b6b6;
        }

        .b-kembali_ws,
        .b-perbaikan_ws {
            background: #c6e0b4;
        }

        .b-kirim_customer,
        .b-pindah {
            background: #bdd7ee;
        }

        .dobel {
            color: #c00000;
            font-weight: bold;
            font-size: 9px;
            display: block;
        }

        .center {
            text-align: center;
        }

        .kecil {
            font-size: 9px;
            color: #555;
            display: block;
        }

        .catatan {
            font-size: 9px;
            color: #555;
            margin-top: 4px;
        }

        .legenda {
            font-size: 10px;
            margin: 4px 0 6px;
            color: #333;
        }

        .legenda span.kotak {
            display: inline-block;
            width: 12px;
            height: 10px;
            vertical-align: middle;
            border-left: 4px solid #70ad47;
            background: #e2efda;
            margin-right: 3px;
        }

        /* ===== Grafik pemakaian bulanan ===== */
        .chart {
            border: 1px solid #8ea9c8;
            padding: 8px 10px;
            margin: 4px 0 8px;
        }

        .chart-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .chart-label {
            width: 110px;
            font-size: 10px;
            flex-shrink: 0;
        }

        .chart-track {
            flex: 1;
            background: #f2f2f2;
            height: 14px;
            border-radius: 2px;
            overflow: hidden;
        }

        .chart-bar {
            height: 100%;
            background: #2e75b6;
        }

        .chart-bar.max {
            background: #548235;
        }

        .chart-bar.min {
            background: #c55a11;
        }

        .chart-val {
            width: 210px;
            font-size: 10px;
            font-weight: bold;
            text-align: right;
            flex-shrink: 0;
        }

        .tag {
            display: inline-block;
            padding: 0 5px;
            border-radius: 6px;
            font-size: 8px;
            color: #fff;
            margin-left: 4px;
        }

        .tag-max {
            background: #548235;
        }

        .tag-min {
            background: #c55a11;
        }

        /* ===== Riwayat penggantian part ===== */
        .petunjuk {
            font-size: 10px;
            color: #444;
            background: #f2f6fb;
            border-left: 4px solid #2e75b6;
            padding: 5px 10px;
            margin: 4px 0 6px;
        }

        .part-judul {
            background: #dbe5f1;
            border: 1px solid #8ea9c8;
            border-bottom: 0;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: bold;
            color: #1f4e79;
        }

        table.data td.selisih {
            text-align: right;
            font-weight: bold;
            font-size: 12px;
            color: #1f4e79;
            background: #eef4fb;
            padding: 4px 6px;
            border-left: 1px solid #bcd0e6;
            border-right: 1px solid #bcd0e6;
        }

        table.data td.selisih.minus {
            color: #c00000;
            background: #fde9e9;
        }

        table.data td.awal {
            text-align: right;
            color: #888;
            font-style: italic;
        }

        table.data tr.ganti-part td {
            border-top: 2px solid #1f4e79;
        }

        table.data tr.grup-tgl td {
            background: #dbe5f1;
            color: #1f4e79;
            font-weight: bold;
            font-size: 11px;
            border-top: 2px solid #2e75b6;
            border-bottom: 1px solid #8ea9c8;
        }

        table.data tr.grup-tgl td .meta {
            font-weight: normal;
            color: #333;
            margin-left: 12px;
        }

        table.data tr.stat td {
            background: #deeaf6;
            font-weight: bold;
            border-top: 2px solid #2e75b6;
        }

        /* Catatan & keterangan (browser-only) */
        .catatan-box {
            margin: 4px 0 8px;
            border: 1px dashed #bf9000;
            background: #fffdf0;
            padding: 6px 8px;
        }

        .catatan-box label {
            display: block;
            font-size: 10px;
            font-weight: bold;
            color: #7f6000;
            margin-bottom: 3px;
        }

        .catatan-box textarea {
            width: 100%;
            min-height: 48px;
            font-family: inherit;
            font-size: 11px;
            padding: 4px 6px;
            border: 1px solid #d9c27a;
            resize: vertical;
        }

        .catatan-cetak {
            display: none;
            white-space: pre-wrap;
            font-size: 10px;
        }

        .ket-input {
            width: 100%;
            border: none;
            border-bottom: 1px dashed #8ea9c8;
            background: transparent;
            font-family: inherit;
            font-size: 10px;
            padding: 2px 3px;
        }

        .ket-input:focus {
            outline: none;
            background: #fffdf0;
        }

        .debug {
            margin-top: 14px;
            padding: 6px 10px;
            font-size: 10px;
            background: #fff8e1;
            border: 1px dashed #bf9000;
            color: #333;
        }

        .tgl-cetak {
            margin-top: 14px;
            font-size: 10px;
            color: #555;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .catatan-box textarea {
                display: none;
            }

            .catatan-box {
                border: 1px solid #bf9000;
            }

            .catatan-cetak {
                display: block;
            }

            table.data tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>

    {{-- ===== Toolbar + Filter Tahun & Part ===== --}}
    <form method="GET" class="toolbar no-print">
        <label for="tahun">Tahun:</label>
        <select name="tahun" id="tahun" onchange="this.form.submit()">
            <option value="semua" @selected(!$tahunDipilih)>Semua Tahun</option>
            @foreach ($daftarTahun as $t)
                <option value="{{ $t }}" @selected($tahunDipilih === $t)>{{ $t }}</option>
            @endforeach
        </select>

        <label for="part">Part:</label>
        <select name="part" id="part" onchange="this.form.submit()">
            <option value="">Semua Part</option>
            @foreach ($daftarPart as $p)
                <option value="{{ $p }}" @selected($partDipilih === $p)>{{ $p }}</option>
            @endforeach
        </select>

        <button type="button" onclick="window.print()">🖨 Cetak</button>
        <button type="button" onclick="window.close()">Tutup</button>
    </form>

    <div class="judul">
        <span>RIWAYAT MESIN — NS: {{ $machine->serial_number }}</span>
        <span>Periode: {{ $periodeLabel }}</span>
    </div>

    {{-- ===== 1. DETAIL MESIN ===== --}}
    <table class="info">
        <tr>
            <th>No Seri</th>
            <td>{{ $machine->serial_number }}</td>
            <th>Customer</th>
            <td>{{ $machine->customer->nama_customer ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tipe / Model</th>
            <td>{{ $tipeMesin ?? '-' }}</td>
            <th>No Kontrak</th>
            <td>{{ $kontrak->no_kontrak ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status Terakhir</th>
            <td>{{ $statusAkhir }}</td>
            <th>Tercatat Sejak</th>
            <td>{{ $tanggalAwal ? $tanggalAwal->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Total Service ({{ $periodeLabel }})</th>
            <td>{{ $jumlahService }}x</td>
            <th>Total Part Diganti ({{ $periodeLabel }})</th>
            <td>{{ $f($grandPart) }} pcs</td>
        </tr>
    </table>

    @if ($kontrak)
        <table class="info">
            <tr>
                <th>Harga Sewa / Bulan</th>
                <td>{{ $kontrak->sewa !== null ? 'Rp ' . $f($kontrak->sewa) : '-' }}</td>
                <th>PPN</th>
                <td>{{ rtrim(rtrim(number_format($kontrak->ppn, 2, ',', '.'), '0'), ',') }}%</td>
            </tr>
            <tr>
                <th>Free Copy BW</th>
                <td>{{ $kontrak->free_bw !== null ? $f($kontrak->free_bw) . ' lembar' : '-' }}</td>
                <th>Free Copy Color</th>
                <td>{{ $kontrak->free_color !== null ? $f($kontrak->free_color) . ' lembar' : '-' }}</td>
            </tr>
            <tr>
                <th>Harga Kelebihan BW</th>
                <td>{{ $kontrak->harga_bw !== null ? 'Rp ' . $f($kontrak->harga_bw) . ' / lembar' : '-' }}</td>
                <th>Harga Kelebihan Color</th>
                <td>{{ $kontrak->harga_color !== null ? 'Rp ' . $f($kontrak->harga_color) . ' / lembar' : '-' }}</td>
            </tr>
        </table>
    @else
        <div class="catatan">Data kontrak MRC belum ditemukan untuk mesin ini, jadi tagihan tidak dihitung.</div>
    @endif

    {{-- ===== 2. PEMAKAIAN PER BULAN + ESTIMASI TAGIHAN ===== --}}
    <div class="sub">
        PEMAKAIAN PER BULAN @if ($adaTagihan)
            &amp; ESTIMASI TAGIHAN
        @endif
        <small>(pemakaian = selisih counter antar kunjungan, dibagi rata per hari)</small>
    </div>

    @if ($bulanIni)
        <div class="box-ini">
            Pemakaian bulan ini ({{ $bulanIni->label }}):
            <span class="angka">{{ $f($bulanIni->bw) }}</span> lembar BW
            @if ($adaColor)
                &nbsp;|&nbsp; <span class="angka">{{ $f($bulanIni->color) }}</span> lembar Color
            @endif
            <div class="ket">
                Baru terhitung {{ $bulanIni->hari }} dari {{ $bulanIni->hari_bulan }} hari (sampai kunjungan
                terakhir).
                @if ($bulanIni->proyeksi_bw)
                    Proyeksi sebulan penuh ± <strong>{{ $f($bulanIni->proyeksi_bw) }}</strong> lembar BW.
                @endif
            </div>
        </div>
    @endif

    {{-- Grafik batang pemakaian BW per bulan --}}
    @if ($barData->isNotEmpty())
        <div class="chart">
            @foreach ($barData as $r)
                @php
                    $lebar = $r->bw > 0 ? max(1, round(($r->bw / $bwMax) * 100, 2)) : 0;
                    $kelas = $r->key === $maxKey ? 'max' : ($r->key === $minKey ? 'min' : '');
                @endphp
                <div class="chart-row">
                    <div class="chart-label">{{ $r->label }}</div>
                    <div class="chart-track">
                        <div class="chart-bar {{ $kelas }}" style="width: {{ $lebar }}%"></div>
                    </div>
                    <div class="chart-val">
                        {{ $f($r->bw) }} lembar
                        @if ($kelas === 'max')
                            <span class="tag tag-max">Terbanyak</span>
                        @elseif ($kelas === 'min')
                            <span class="tag tag-min">Terendah</span>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="catatan">Bulan Parsial tetap ditampilkan, tetapi tidak ikut dalam penentuan terbanyak dan
                terendah.</div>
        </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th class="l">Bulan</th>
                <th class="c" style="width:70px">Kunjungan</th>
                <th class="r">Pemakaian BW</th>
                @if ($adaColor)
                    <th class="r">Pemakaian Color</th>
                @endif
                @if ($adaTagihan)
                    <th class="tagihan r">Free Copy{{ $adaColor ? ' (BW/Color)' : '' }}</th>
                    <th class="tagihan r">Kelebihan{{ $adaColor ? ' (BW/Color)' : '' }}</th>
                    <th class="tagihan r">Harga Sewa</th>
                    <th class="tagihan r">Biaya Kelebihan</th>
                    <th class="tagihan r">Subtotal</th>
                    <th class="tagihan r">PPN {{ rtrim(rtrim(number_format($kontrak->ppn, 2, ',', '.'), '0'), ',') }}%
                    </th>
                    <th class="tagihan r">Est. Tagihan (acuan)</th>
                @endif
                <th class="c" style="width:70px">Ket.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deret as $r)
                @php $t = $r->tagihan; @endphp
                <tr class="{{ $r->kosong ? 'kosong' : ($r->key === now()->format('Y-m') ? 'ini' : '') }}">
                    <td>{{ $r->label }}</td>
                    @if ($r->kosong)
                        <td class="center">-</td>
                        <td class="angka">-</td>
                        @if ($adaColor)
                            <td class="angka">-</td>
                        @endif
                        @if ($adaTagihan)
                            @for ($k = 0; $k < 7; $k++)
                                <td class="angka">-</td>
                            @endfor
                        @endif
                        <td class="center">-</td>
                    @else
                        <td class="center">{{ $r->baca }}x</td>
                        <td class="tebal">{{ $f($r->bw) }}</td>
                        @if ($adaColor)
                            <td class="tebal">{{ $f($r->color) }}</td>
                        @endif
                        @if ($adaTagihan)
                            <td class="angka">{{ $f($t->free_bw) }}@if ($adaColor)
                                    / {{ $f($t->free_clr) }}
                                @endif
                            </td>
                            <td class="angka">{{ $f($t->lebih_bw) }}@if ($adaColor)
                                    / {{ $f($t->lebih_clr) }}
                                @endif
                            </td>
                            <td class="angka">{{ $f($t->sewa) }}</td>
                            <td class="angka">{{ $f($t->biaya) }}</td>
                            <td class="angka">{{ $f($t->subtotal) }}</td>
                            <td class="angka">{{ $f($t->ppn) }}</td>
                            <td class="total-tagihan">{{ $f($t->total) }}</td>
                        @endif
                        <td class="center st-{{ strtolower($r->status) }}">{{ $r->status }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL {{ $periodeLabel }}</td>
                <td class="angka">{{ $f($tot->bw) }}</td>
                @if ($adaColor)
                    <td class="angka">{{ $f($tot->color) }}</td>
                @endif
                @if ($adaTagihan)
                    <td></td>
                    <td></td>
                    <td class="angka">{{ $f($tot->sewa) }}</td>
                    <td class="angka">{{ $f($tot->biaya) }}</td>
                    <td class="angka">{{ $f($tot->subtotal) }}</td>
                    <td class="angka">{{ $f($tot->ppn) }}</td>
                    <td class="angka">{{ $f($tot->total) }}</td>
                @endif
                <td></td>
            </tr>
            @if ($rata->bw !== null)
                <tr>
                    <td colspan="2">Rata-rata / bulan ({{ $rata->n }} bulan penuh)</td>
                    <td class="angka">{{ $f($rata->bw) }}</td>
                    @if ($adaColor)
                        <td class="angka">{{ $f($rata->color) }}</td>
                    @endif
                    @if ($adaTagihan)
                        <td colspan="6"></td>
                        <td class="angka">{{ $f($rata->total) }}</td>
                    @endif
                    <td></td>
                </tr>
            @endif
        </tfoot>
    </table>

    <div class="catatan">
        Terukur = ada kunjungan pada bulan itu. Estimasi = tanpa kunjungan (prorata dari kunjungan sebelum dan
        sesudahnya).
        Parsial = bulan belum terhitung penuh.
        @if ($adaTagihan)
            <strong>Nilai tagihan hanya acuan hitungan dari data kontrak, bukan tagihan resmi.</strong>
        @endif
        @if ($anomali > 0)
            <span style="color:#c00000">⚠ {{ $anomali }} pembacaan counter mundur dilewati (kemungkinan salah
                input).</span>
        @endif
    </div>

    {{-- ===== 3. RIWAYAT PENGGANTIAN PART (tabel biasa + filter Part) ===== --}}
    <div class="sub">
        RIWAYAT PENGGANTIAN PART
        @if ($partDipilih)
            — {{ $partDipilih }}
        @endif
        <small>(selisih = kenaikan counter sejak penggantian part yang sama sebelumnya)</small>
    </div>

    {{-- Catatan bebas, disimpan di browser ini saja (localStorage) --}}
    <div class="catatan-box no-print-wrap">
        <label for="catatan-browser">Catatan (tersimpan di browser ini saja, tidak ke server)</label>
        <textarea id="catatan-browser" class="no-print" placeholder="Ketik catatan untuk mesin ini..."></textarea>
        <div class="catatan-cetak" id="catatan-cetak"></div>
    </div>

    @php
        // Semua Part: urut nama part lalu tanggal, supaya penggantian part yang sama berdekatan.
        // 1 Part: urut tanggal.
        $barisPart = $partDipilih
            ? $gantiTampil
            : $gantiTampil->sortBy(fn($r) => $r->part . '|' . $r->tanggal->format('Y-m-d'))->values();
        $partSebelumnya = null;
    @endphp

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width:30px">No</th>
                <th class="c" style="width:75px">Tanggal</th>
                <th class="l">Nama Part</th>
                <th class="c" style="width:40px">Qty</th>
                <th class="r" style="width:100px">Counter BW Lalu</th>
                <th class="r" style="width:110px">Counter BW Sekarang</th>
                <th class="r" style="width:100px">Selisih BW</th>
                @if ($adaColorPart)
                    <th class="r" style="width:100px">Counter Color Lalu</th>
                    <th class="r" style="width:110px">Counter Color Sekarang</th>
                    <th class="r" style="width:100px">Selisih Color</th>
                @endif
                <th class="r" style="width:80px">Selisih Hari</th>
                <th class="l" style="width:100px">Teknisi</th>
                <th class="l" style="width:180px">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barisPart as $i => $r)
                @php
                    $laluBw = $r->selisih_bw !== null && $r->bw ? $r->bw - $r->selisih_bw : null;
                    $laluClr = $r->selisih_clr !== null && $r->color ? $r->color - $r->selisih_clr : null;
                    $pindahPart = !$partDipilih && $partSebelumnya !== null && $partSebelumnya !== $r->part;
                    $partSebelumnya = $r->part;
                @endphp
                <tr class="{{ $pindahPart ? 'ganti-part' : '' }}">
                    <td class="center">{{ $i + 1 }}</td>
                    <td class="center">{{ $r->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $r->part }}</td>
                    <td class="center">{{ $r->qty }}</td>
                    @if ($laluBw !== null)
                        <td class="angka">{{ $f($laluBw) }}</td>
                    @else
                        <td class="awal">(awal)</td>
                    @endif
                    <td class="angka">{{ $r->bw ? $f($r->bw) : '-' }}</td>
                    <td class="selisih {{ $r->selisih_bw !== null && $r->selisih_bw < 0 ? 'minus' : '' }}">
                        {{ $fmtSel($r->selisih_bw) }}
                    </td>
                    @if ($adaColorPart)
                        @if ($laluClr !== null)
                            <td class="angka">{{ $f($laluClr) }}</td>
                        @else
                            <td class="awal">(awal)</td>
                        @endif
                        <td class="angka">{{ $r->color ? $f($r->color) : '-' }}</td>
                        <td class="selisih {{ $r->selisih_clr !== null && $r->selisih_clr < 0 ? 'minus' : '' }}">
                            {{ $fmtSel($r->selisih_clr) }}
                        </td>
                    @endif
                    <td class="angka">{{ $r->hari !== null ? $r->hari . ' hari' : '-' }}</td>
                    <td>{{ $r->teknisi ?? '-' }}</td>
                    <td>
                        <input type="text" class="ket-input" data-key="{{ $kunciKet($r) }}"
                            placeholder="Ketik keterangan..." aria-label="Keterangan {{ $r->part }}">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $adaColorPart ? 13 : 10 }}" class="center">Tidak ada penggantian part pada
                        {{ $periodeLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($partDipilih && $selList->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right">Rata-rata selisih ({{ $selList->count() }}x
                        pergantian)</td>
                    <td class="angka">{{ $f($selList->avg()) }}</td>
                    @if ($adaColorPart)
                        <td colspan="3"></td>
                    @endif
                    <td class="angka">{{ $hariList->count() ? $f($hariList->avg()) . ' hari' : '-' }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align:right">Terpendek / Terpanjang</td>
                    <td class="angka">{{ $f($selList->min()) }} / {{ $f($selList->max()) }}</td>
                    @if ($adaColorPart)
                        <td colspan="3"></td>
                    @endif
                    <td class="angka">
                        {{ $hariList->count() ? $f($hariList->min()) . ' / ' . $f($hariList->max()) : '-' }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
    <div class="catatan">"(awal)" = penggantian pertama yang tercatat untuk part itu, jadi belum ada pembanding.</div>

    @if ($totalTampil->isNotEmpty())
        <div class="sub">TOTAL PART YANG SUDAH DIGANTI <small>({{ $periodeLabel }})</small></div>
        <table class="data">
            <thead>
                <tr>
                    <th class="c" style="width:30px">No</th>
                    <th class="l">Nama Part</th>
                    <th class="c" style="width:90px">Total Qty</th>
                    <th class="c" style="width:90px">Kali Diganti</th>
                    <th class="c" style="width:110px">Terakhir Diganti</th>
                    <th class="r" style="width:150px">Rata-rata Selisih BW</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($totalTampil as $i => $p)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $p->nama }}</td>
                        <td class="center"><strong>{{ $f($p->total) }}</strong></td>
                        <td class="center">{{ $p->kali }}x</td>
                        <td class="center">{{ $p->terakhir ? $p->terakhir->format('d/m/Y') : '-' }}</td>
                        <td class="angka">{{ $p->avg_bw !== null ? $f($p->avg_bw) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right">TOTAL</td>
                    <td class="center">{{ $f($totalTampil->sum('total')) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ===== 4. RIWAYAT KEJADIAN ===== --}}
    <div class="sub">RIWAYAT KEJADIAN <small>({{ $periodeLabel }})</small></div>

    @if ($jumlahKembar > 0 || $jumlahDobel > 0)
        <div class="legenda">
            @if ($jumlahKembar > 0)
                <span class="kotak"></span>
                Baris berwarna = ada lebih dari 1 event pada tanggal yang sama ({{ $jumlahKembar }} tanggal).
            @endif
            @if ($jumlahDobel > 0)
                &nbsp;&nbsp;
                <strong style="color:#c00000">⚠</strong>
                = Service dengan isi persis sama pada tanggal yang sama, kemungkinan input dobel ({{ $jumlahDobel }}
                kasus).
            @endif
        </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width:70px">Tanggal</th>
                <th class="l" style="width:100px">Event</th>
                <th class="l" style="width:85px">Tipe Kunjungan</th>
                <th class="l" style="width:150px">Customer</th>
                <th class="l" style="width:100px">Teknisi</th>
                <th class="l">Detail</th>
            </tr>
        </thead>
        <tbody>
            @php $siklusSebelumnya = null; @endphp
            @forelse ($events as $e)
                @if ($e->siklus !== $siklusSebelumnya)
                    <tr class="siklus">
                        <td colspan="6">{{ $e->siklus === 0 ? 'AWAL / STOK' : 'SIKLUS KE-' . $e->siklus }}</td>
                    </tr>
                    @php $siklusSebelumnya = $e->siklus; @endphp
                @endif

                <tr class="{{ $e->grup_tgl !== null ? 'tgl-' . $e->grup_tgl % 4 : '' }}">
                    <td class="center">
                        {{ $e->tanggal->format('d/m/Y') }}
                        @if ($e->grup_tgl !== null)
                            <span class="kecil">{{ $e->jml_tgl }} event</span>
                        @endif
                    </td>
                    <td><span class="badge b-{{ $e->tipe }}">{{ $e->tipe_label }}</span></td>
                    <td>{{ $e->tipe_kunjungan ?? '-' }}</td>
                    <td>{{ $e->customer->nama_customer ?? '-' }}</td>
                    <td>{{ $e->teknisi_nama ?? '-' }}</td>
                    <td>
                        @if ($e->sparepart)
                            {{ $e->sparepart->nama_sparepart }}
                            @if ($e->jumlah)
                                ({{ $e->jumlah }})
                            @endif
                        @endif

                        @if ($e->tipe !== 'ganti_part' && $e->keterangan)
                            {{ $e->keterangan }}
                        @endif

                        @if ($e->cbw || $e->ccolor)
                            <span class="kecil">
                                @if ($e->cbw)
                                    Counter BW: {{ $f($e->cbw) }}
                                @endif
                                @if ($e->ccolor)
                                    &nbsp; Color: {{ $f($e->ccolor) }}
                                @endif
                            </span>
                        @endif

                        @if (!empty($e->dobel))
                            <span class="dobel">⚠ kemungkinan dobel</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Tidak ada kejadian pada {{ $periodeLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="tgl-cetak">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>

    {{-- Hanya tampil di layar (tidak ikut tercetak): kolom kontrak yang terbaca --}}
    @if ($kontrak)
        <div class="debug no-print">
            <strong>Kolom MrcContract yang terbaca:</strong>
            @foreach ($kontrak->sumber as $field => $kolom)
                {{ $field }} = <strong>{{ $kolom ?? 'TIDAK DITEMUKAN' }}</strong>
                @if (!$loop->last)
                    ,
                @endif
            @endforeach
            <br>Jika ada yang salah, isi nama kolom yang benar di <code>KONTRAK_KOLOM</code> pada
            MachineRiwayatController.
        </div>
    @endif

    <script>
        (function() {
            const PREFIX = @json('riwayat-mesin-' . $machine->serial_number);

            // Akses localStorage dibungkus try/catch: bisa gagal di mode privat atau jika diblokir browser.
            const simpan = (kunci, nilai) => {
                try {
                    localStorage.setItem(kunci, nilai);
                } catch (e) {
                    /* abaikan */ }
            };
            const baca = (kunci) => {
                try {
                    return localStorage.getItem(kunci) ?? '';
                } catch (e) {
                    return '';
                }
            };

            // Catatan umum
            const catatan = document.getElementById('catatan-browser');
            const catatanCetak = document.getElementById('catatan-cetak');
            const kunciCatatan = PREFIX + '-catatan';

            catatan.value = baca(kunciCatatan);
            catatanCetak.textContent = catatan.value;

            catatan.addEventListener('input', () => {
                simpan(kunciCatatan, catatan.value);
                catatanCetak.textContent = catatan.value;
            });

            // Keterangan per baris penggantian part
            document.querySelectorAll('.ket-input').forEach((el) => {
                const kunci = PREFIX + '-ket-' + el.dataset.key;
                el.value = baca(kunci);
                el.addEventListener('input', () => simpan(kunci, el.value));
            });
        })();
    </script>
</body>

</html>
