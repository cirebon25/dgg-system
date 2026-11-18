<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Mesin per Rayon - {{ $namaBulan }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            padding: 12px 16px;
            color: #000;
            line-height: 1.3;
            background: #fff;
        }

        header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        header h1 {
            font-size: 16px;
        }

        header h2 {
            font-size: 12px;
            margin: 3px 0;
        }

        /* ===== Toolbar filter (tidak ikut tercetak) ===== */
        .toolbar {
            background: #f4f4f4;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            align-items: center;
        }

        .toolbar label {
            font-weight: 600;
            font-size: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .toolbar input[type=text],
        .toolbar select {
            padding: 4px 6px;
            border: 1px solid #aaa;
            border-radius: 4px;
            font-size: 11px;
        }

        .toolbar input[type=text] {
            min-width: 220px;
        }

        .toolbar button {
            padding: 5px 12px;
            border: 1px solid #333;
            background: #333;
            color: #fff;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }

        .toolbar .btn-light {
            background: #fff;
            color: #333;
        }

        .toolbar .hitung {
            margin-left: auto;
            font-weight: 600;
        }

        .toolbar .baris {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            align-items: center;
            width: 100%;
        }

        /* ===== Ringkasan: diagram batang ranking customer ===== */
        .summary {
            border: 1.5px solid #000;
            margin-bottom: 14px;
        }

        .summary-head {
            background: #e9e9e9;
            border-bottom: 1px solid #000;
            padding: 5px 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 14px;
            align-items: baseline;
        }

        .summary-head h3 {
            font-size: 12px;
            text-transform: uppercase;
        }

        .summary-body {
            padding: 6px 8px;
        }

        .rank-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .rank-no {
            width: 26px;
            text-align: right;
            color: #555;
        }

        .rank-nama {
            width: 240px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .rank-track {
            flex: 1;
            height: 12px;
            display: flex;
            background: #f3f3f3;
        }

        .rank-bw {
            background: #444;
            height: 100%;
        }

        .rank-color {
            background: #e67e22;
            height: 100%;
        }

        .rank-val {
            width: 210px;
            text-align: right;
            white-space: nowrap;
        }

        .rank-val b {
            font-weight: 700;
        }

        .rank-val small {
            color: #555;
            font-size: 9px;
        }

        .legend {
            font-size: 9px;
            font-weight: 400;
            text-transform: none;
            margin-left: 8px;
        }

        .legend i {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin: 0 3px 0 8px;
            vertical-align: middle;
        }

        /* ===== Rayon ===== */
        .rayon-box {
            margin-bottom: 16px;
        }

        .rayon-title {
            background: #333;
            color: #fff;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
        }

        /* ===== Kartu per no seri ===== */
        .mesin-card {
            border: 1.5px solid #000;
            margin-bottom: 12px;
            page-break-inside: avoid;
            break-inside: avoid;
            font-size: 11px;
        }

        .mesin-head {
            background: #222;
            color: #fff;
            padding: 6px 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 14px;
            align-items: center;
        }

        .mesin-head .sn {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .mesin-head .cust {
            margin-left: auto;
            font-weight: 600;
            font-size: 12px;
        }

        .badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 700;
            background: #fff;
            color: #000;
        }

        .mesin-body {
            display: grid;
            grid-template-columns: 290px 1fr;
        }

        .kolom-kiri {
            border-right: 1px solid #000;
            background: #fafafa;
        }

        .kolom-kanan {
            min-width: 0;
        }

        .blok {
            padding: 6px 10px;
            border-bottom: 1px solid #ccc;
        }

        .blok:last-child {
            border-bottom: none;
        }

        .blok h4 {
            font-size: 9px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .kv {
            display: flex;
            gap: 6px;
            padding: 1.5px 0;
        }

        .kv .k {
            width: 98px;
            flex-shrink: 0;
            color: #555;
        }

        .kv .v {
            font-weight: 600;
        }

        .warn {
            color: #b00020;
        }

        /* ===== Angka ringkas ===== */
        .kpi-row {
            display: flex;
            border-bottom: 1px solid #000;
        }

        .kpi {
            flex: 1;
            padding: 6px 10px;
            border-right: 1px solid #ccc;
        }

        .kpi:last-child {
            border-right: none;
        }

        .kpi .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: .4px;
        }

        .kpi .nilai {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.25;
        }

        .kpi .nilai small {
            font-size: 9px;
            font-weight: 400;
            color: #555;
        }

        .kpi .sub {
            font-size: 9px;
            color: #555;
        }

        .section {
            padding: 6px 10px;
            border-bottom: 1px solid #ccc;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section h4 {
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        /* ===== Grafik per mesin ===== */
        .chart-wrap {
            margin-left: 54px;
            margin-right: 70px;
        }

        .chart {
            display: flex;
            align-items: flex-end;
            height: 48px;
            border-bottom: 1px solid #000;
        }

        .chart .grp {
            flex: 1;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 1px;
        }

        .chart .grp.aktif {
            background: #fff3c4;
        }

        .bar {
            width: 38%;
            min-height: 0;
        }

        .bar-bw {
            background: #444;
        }

        .bar-color {
            background: #e67e22;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #eee;
            border: 1px solid #999;
            padding: 3px 5px;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #bbb;
            padding: 3px 5px;
            vertical-align: top;
        }

        .usage-table {
            table-layout: fixed;
        }

        .usage-table th:first-child,
        .usage-table td:first-child {
            width: 54px;
            text-align: left;
        }

        .usage-table th:last-child,
        .usage-table td:last-child {
            width: 70px;
        }

        .usage-table td {
            text-align: right;
            font-size: 10px;
        }

        .usage-table td.nol {
            color: #bbb;
        }

        .usage-table th.aktif,
        .usage-table td.aktif {
            background: #fff3c4;
        }

        .usage-table td.total {
            font-weight: 700;
            background: #f2f2f2;
        }

        .service-table {
            table-layout: fixed;
        }

        .service-table td {
            font-size: 10.5px;
        }

        .tipe {
            display: inline-block;
            min-width: 26px;
            text-align: center;
            padding: 0 5px;
            border: 1px solid #000;
            border-radius: 3px;
            font-weight: 700;
            font-size: 9.5px;
        }

        .text-center {
            text-align: center;
        }

        .empty {
            color: #777;
            font-style: italic;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>DGG SYSTEM - MONITORING UNIT</h1>
        <h2>LAPORAN DETAIL MESIN, PEMAKAIAN & SERVICE PER NO SERI</h2>
        <p>Service bulan {{ $namaBulan }} {{ $year }} &nbsp;|&nbsp; Grafik pemakaian tahun
            {{ $year }}</p>
    </header>

    @php
        $semuaMesin = $rayonData->flatMap(fn($r) => $r->mesin);
        $daftarStatus = $semuaMesin->map(fn($m) => $m->machine->status)->filter()->unique()->sort()->values();
        $namaBulanList = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];
    @endphp

    {{-- ================= FILTER ================= --}}
    <div class="toolbar no-print">
        <div class="baris">
            <form method="GET" action="{{ url()->current() }}" style="display:flex; gap:6px; align-items:center;">
                <label>Bulan
                    <select name="month">
                        @foreach ($namaBulanList as $no => $nama)
                            <option value="{{ $no }}" @selected($no === (int) $month)>{{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>Tahun
                    <select name="year">
                        @foreach (range(now()->year, now()->year - 5) as $th)
                            <option value="{{ $th }}" @selected($th === (int) $year)>{{ $th }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <button type="submit">Tampilkan</button>
            </form>

            <label>Cari
                <input type="text" id="f-cari" placeholder="No seri / customer / kota / model">
            </label>

            <label>Rayon
                <select id="f-rayon">
                    <option value="">Semua</option>
                    @foreach ($rayonData as $r)
                        <option value="{{ $r->nama }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </label>

            <label>Status
                <select id="f-status">
                    <option value="">Semua</option>
                    @foreach ($daftarStatus as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                    @endforeach
                </select>
            </label>

            <label><input type="checkbox" id="f-service"> Ada service bulan ini</label>
            <label><input type="checkbox" id="f-kontrak"> Punya kontrak MRC</label>
        </div>

        <div class="baris">
            <label>Diagram periode
                <select id="f-periode">
                    <option value="tahun">Setahun {{ $year }}</option>
                    <option value="bulan">{{ $namaBulan }} {{ $year }}</option>
                </select>
            </label>

            <label>Tampilkan
                <select id="f-top">
                    <option value="0">Semua customer</option>
                    <option value="10">Top 10</option>
                    <option value="20">Top 20</option>
                    <option value="50">Top 50</option>
                </select>
            </label>

            <button type="button" class="btn-light" id="f-reset">Reset</button>
            <button type="button" onclick="window.print()">Cetak</button>
            <span class="hitung">Menampilkan <span id="f-jumlah">{{ $semuaMesin->count() }}</span> dari
                {{ $semuaMesin->count() }} mesin</span>
        </div>
    </div>

    {{-- ================= DIAGRAM RANKING CUSTOMER ================= --}}
    <div class="summary">
        <div class="summary-head">
            <h3>Ranking Pemakaian Customer</h3>
            <span id="sum-periode"></span>
            <span id="sum-total"></span>
            <span class="legend"><i style="background:#444"></i>BW<i style="background:#e67e22"></i>Color</span>
        </div>
        <div class="summary-body">
            <div id="sum-chart"></div>
            <div class="empty" id="sum-kosong" style="display:none;">Tidak ada customer yang cocok dengan filter.</div>
        </div>
    </div>

    {{-- ================= DETAIL MESIN ================= --}}
    @foreach ($rayonData as $rayon)
        <div class="rayon-box" data-rayon-box>
            <div class="rayon-title">📍 RAYON: {{ strtoupper($rayon->nama) }} &nbsp;|&nbsp;
                {{ $rayon->mesin->count() }} Mesin</div>

            @forelse ($rayon->mesin as $row)
                @php
                    $m = $row->machine;
                    $c = $row->customer;
                    $k = $row->kontrak;
                    $bw = $row->pemakaian['bw'];
                    $color = $row->pemakaian['color'];
                    $maks = max(1, max($bw), max($color));
                    $bulanIni = (int) $month;
                    $rupiah = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
                    $angka = fn($v) => number_format((float) $v, 0, ',', '.');

                    // Acuan kelebihan bulan ini terhadap free copy (bukan tagihan real)
                    $lebihBw = $k ? max(0, $bw[$bulanIni] - (int) $k->free_bw) : 0;
                    $lebihColor = $k ? max(0, $color[$bulanIni] - (int) $k->free_color) : 0;
                @endphp

                <div class="mesin-card"
                    data-cari="{{ strtolower($m->serial_number . ' ' . $c->nama_customer . ' ' . $c->kota . ' ' . $m->tipe_model) }}"
                    data-rayon="{{ $rayon->nama }}" data-status="{{ $m->status }}"
                    data-service="{{ $row->logs->count() }}" data-kontrak="{{ $k ? 1 : 0 }}"
                    data-customer="{{ $c->nama_customer }}" data-bw-tahun="{{ array_sum($bw) }}"
                    data-color-tahun="{{ array_sum($color) }}" data-bw-bulan="{{ $bw[$bulanIni] }}"
                    data-color-bulan="{{ $color[$bulanIni] }}">

                    {{-- ===== HEADER: identitas singkat ===== --}}
                    <div class="mesin-head">
                        <span class="sn">{{ $m->serial_number }}</span>
                        <span>{{ $m->tipe_model }}</span>
                        <span class="badge">{{ $m->status ?? '-' }}</span>
                        <span class="cust">{{ $c->nama_customer }} &middot; {{ $c->kota }}</span>
                    </div>

                    <div class="mesin-body">

                        {{-- ===== KOLOM KIRI: mesin, customer, kontrak ===== --}}
                        <div class="kolom-kiri">
                            <div class="blok">
                                <h4>Mesin</h4>
                                <div class="kv"><span class="k">No Seri</span><span
                                        class="v">{{ $m->serial_number }}</span></div>
                                <div class="kv"><span class="k">Model</span><span
                                        class="v">{{ $m->tipe_model }}</span></div>
                                <div class="kv"><span class="k">Status</span><span
                                        class="v">{{ $m->status ?? '-' }}</span></div>
                            </div>

                            <div class="blok">
                                <h4>Customer</h4>
                                <div class="kv"><span class="k">Nama</span><span
                                        class="v">{{ $c->nama_customer }}</span></div>
                                <div class="kv"><span class="k">Kota</span><span
                                        class="v">{{ $c->kota ?? '-' }}</span></div>
                                <div class="kv"><span class="k">Rayon</span><span
                                        class="v">{{ $rayon->nama }}</span></div>
                            </div>

                            <div class="blok">
                                <h4>Kontrak MRC</h4>
                                @if ($k)
                                    <div class="kv"><span class="k">Mulai</span><span
                                            class="v">{{ $k->tanggal_mulai ? \Carbon\Carbon::parse($k->tanggal_mulai)->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="kv"><span class="k">Selesai</span><span
                                            class="v">{{ $k->tanggal_selesai ? \Carbon\Carbon::parse($k->tanggal_selesai)->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="kv"><span class="k">Harga Sewa</span><span
                                            class="v">{{ $rupiah($k->harga_sewa) }}</span></div>
                                    <div class="kv"><span class="k">Free BW</span><span
                                            class="v">{{ $angka($k->free_bw) }} lbr</span></div>
                                    <div class="kv"><span class="k">Free Color</span><span
                                            class="v">{{ $angka($k->free_color) }} lbr</span></div>
                                    <div class="kv"><span class="k">Lebih BW</span><span
                                            class="v">{{ $rupiah($k->harga_bw) }} /lbr</span></div>
                                    <div class="kv"><span class="k">Lebih Color</span><span
                                            class="v">{{ $rupiah($k->harga_color) }} /lbr</span></div>

                                    @if ($lebihBw > 0 || $lebihColor > 0)
                                        <div class="kv warn"
                                            style="margin-top:4px; border-top:1px dashed #999; padding-top:4px;">
                                            <span class="k warn">Lebih {{ $namaBulanList[$bulanIni] }}</span>
                                            <span class="v">
                                                @if ($lebihBw > 0)
                                                    BW {{ $angka($lebihBw) }} lbr (&asymp;
                                                    {{ $rupiah($lebihBw * $k->harga_bw) }})
                                                @endif
                                                @if ($lebihBw > 0 && $lebihColor > 0)
                                                    <br>
                                                @endif
                                                @if ($lebihColor > 0)
                                                    Color {{ $angka($lebihColor) }} lbr (&asymp;
                                                    {{ $rupiah($lebihColor * $k->harga_color) }})
                                                @endif
                                                <br><small style="font-weight:400;">acuan, bukan tagihan real</small>
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <span class="empty">Tidak ada kontrak MRC aktif</span>
                                @endif
                            </div>
                        </div>

                        {{-- ===== KOLOM KANAN: angka ringkas, pemakaian, service ===== --}}
                        <div class="kolom-kanan">

                            <div class="kpi-row">
                                <div class="kpi">
                                    <div class="label">Pemakaian {{ $namaBulanList[$bulanIni] }}</div>
                                    <div class="nilai">{{ $angka($bw[$bulanIni] + $color[$bulanIni]) }}
                                        <small>lbr</small></div>
                                    <div class="sub">BW {{ $angka($bw[$bulanIni]) }} &nbsp;|&nbsp; Color
                                        {{ $angka($color[$bulanIni]) }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Total {{ $year }}</div>
                                    <div class="nilai">{{ $angka(array_sum($bw) + array_sum($color)) }}
                                        <small>lbr</small></div>
                                    <div class="sub">BW {{ $angka(array_sum($bw)) }} &nbsp;|&nbsp; Color
                                        {{ $angka(array_sum($color)) }}</div>
                                </div>
                                <div class="kpi">
                                    <div class="label">Service {{ $namaBulanList[$bulanIni] }}</div>
                                    <div class="nilai">{{ $row->logs->count() }} <small>kunjungan</small></div>
                                    <div class="sub">
                                        {{ $row->logs->pluck('tipe_kunjungan')->filter()->countBy()->map(fn($n, $t) => $t . ' x' . $n)->implode(', ') ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h4>
                                    Pemakaian per bulan {{ $year }}
                                    <span class="legend"><i style="background:#444"></i>BW<i
                                            style="background:#e67e22"></i>Color</span>
                                </h4>

                                <div class="chart-wrap">
                                    <div class="chart">
                                        @foreach (range(1, 12) as $b)
                                            <div class="grp {{ $b === $bulanIni ? 'aktif' : '' }}"
                                                title="{{ $namaBulanList[$b] }}: BW {{ $angka($bw[$b]) }}, Color {{ $angka($color[$b]) }}">
                                                <div class="bar bar-bw"
                                                    style="height: {{ round(($bw[$b] / $maks) * 100) }}%"></div>
                                                <div class="bar bar-color"
                                                    style="height: {{ round(($color[$b] / $maks) * 100) }}%"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <table class="usage-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @foreach (range(1, 12) as $b)
                                                <th class="{{ $b === $bulanIni ? 'aktif' : '' }}">
                                                    {{ $namaBulanList[$b] }}</th>
                                            @endforeach
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>BW</b></td>
                                            @foreach (range(1, 12) as $b)
                                                <td
                                                    class="{{ $b === $bulanIni ? 'aktif' : '' }} {{ $bw[$b] ? '' : 'nol' }}">
                                                    {{ $bw[$b] ? $angka($bw[$b]) : '-' }}</td>
                                            @endforeach
                                            <td class="total">{{ $angka(array_sum($bw)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Color</b></td>
                                            @foreach (range(1, 12) as $b)
                                                <td
                                                    class="{{ $b === $bulanIni ? 'aktif' : '' }} {{ $color[$b] ? '' : 'nol' }}">
                                                    {{ $color[$b] ? $angka($color[$b]) : '-' }}</td>
                                            @endforeach
                                            <td class="total">{{ $angka(array_sum($color)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="section">
                                <h4>Riwayat service {{ $namaBulan }} {{ $year }}</h4>
                                @if ($row->logs->isEmpty())
                                    <div class="empty">- Tidak ada service pada periode ini -</div>
                                @else
                                    <table class="service-table">
                                        <thead>
                                            <tr>
                                                <th style="width:72px;">Tanggal</th>
                                                <th style="width:48px;">Tipe</th>
                                                <th style="width:32%;">Kerusakan</th>
                                                <th>Perbaikan</th>
                                                <th style="width:110px;">Teknisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($row->logs as $log)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-center"><span
                                                            class="tipe">{{ $log->tipe_kunjungan ?? '-' }}</span>
                                                    </td>
                                                    <td>{{ $log->kerusakan ?? ($log->keluhan ?? '-') }}</td>
                                                    <td>{{ $log->perbaikan ?? ($log->tindakan ?? '-') }}</td>
                                                    <td>
                                                        {{ $log->technician?->nama_technician ?? '-' }}
                                                        @if (!empty($log->nama_teknisi_2))
                                                            <br>{{ $log->nama_teknisi_2 }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty" style="padding:6px;">Tidak ada mesin terpasang di wilayah ini.</div>
            @endforelse
        </div>
    @endforeach

    {{-- ================= SCRIPT FILTER + DIAGRAM ================= --}}
    <script>
        (function() {
            const cari = document.getElementById('f-cari');
            const rayon = document.getElementById('f-rayon');
            const status = document.getElementById('f-status');
            const service = document.getElementById('f-service');
            const kontrak = document.getElementById('f-kontrak');
            const periode = document.getElementById('f-periode');
            const top = document.getElementById('f-top');
            const jumlah = document.getElementById('f-jumlah');

            const chart = document.getElementById('sum-chart');
            const kosong = document.getElementById('sum-kosong');
            const sumPer = document.getElementById('sum-periode');
            const sumTot = document.getElementById('sum-total');

            const labelPeriode = {
                tahun: 'Setahun {{ $year }}',
                bulan: '{{ $namaBulan }} {{ $year }}'
            };

            const fmt = function(n) {
                return Number(n).toLocaleString('id-ID');
            };

            function buatBaris(no, item, maks) {
                const baris = document.createElement('div');
                baris.className = 'rank-row';

                const elNo = document.createElement('div');
                elNo.className = 'rank-no';
                elNo.textContent = no + '.';

                const elNama = document.createElement('div');
                elNama.className = 'rank-nama';
                elNama.textContent = item.nama;
                elNama.title = item.nama;

                const track = document.createElement('div');
                track.className = 'rank-track';

                const bw = document.createElement('div');
                bw.className = 'rank-bw';
                bw.style.width = (item.bw / maks * 100) + '%';

                const color = document.createElement('div');
                color.className = 'rank-color';
                color.style.width = (item.color / maks * 100) + '%';

                track.appendChild(bw);
                track.appendChild(color);

                const elVal = document.createElement('div');
                elVal.className = 'rank-val';
                const total = document.createElement('b');
                total.textContent = fmt(item.bw + item.color);
                const rinci = document.createElement('small');
                rinci.textContent = '  (BW ' + fmt(item.bw) + ' / C ' + fmt(item.color) + ')';
                elVal.appendChild(total);
                elVal.appendChild(rinci);

                baris.appendChild(elNo);
                baris.appendChild(elNama);
                baris.appendChild(track);
                baris.appendChild(elVal);

                return baris;
            }

            function gambarDiagram(kartuTampil) {
                const kunci = periode.value; // 'tahun' / 'bulan'
                const peta = {};

                kartuTampil.forEach(function(k) {
                    const nama = k.dataset.customer || '-';
                    if (!peta[nama]) peta[nama] = {
                        nama: nama,
                        bw: 0,
                        color: 0
                    };
                    peta[nama].bw += Number(k.dataset['bw' + (kunci === 'tahun' ? 'Tahun' : 'Bulan')]) || 0;
                    peta[nama].color += Number(k.dataset['color' + (kunci === 'tahun' ? 'Tahun' : 'Bulan')]) ||
                        0;
                });

                let daftar = Object.values(peta).sort(function(a, b) {
                    return (b.bw + b.color) - (a.bw + a.color);
                });

                const jumlahCustomer = daftar.length;
                const batas = Number(top.value);
                if (batas > 0) daftar = daftar.slice(0, batas);

                const totalBw = daftar.reduce(function(s, d) {
                    return s + d.bw;
                }, 0);
                const totalColor = daftar.reduce(function(s, d) {
                    return s + d.color;
                }, 0);
                const maks = Math.max(1, ...daftar.map(function(d) {
                    return d.bw + d.color;
                }));

                chart.innerHTML = '';
                daftar.forEach(function(item, i) {
                    chart.appendChild(buatBaris(i + 1, item, maks));
                });

                kosong.style.display = daftar.length ? 'none' : '';
                sumPer.textContent = labelPeriode[kunci] + ' | ' + daftar.length + ' dari ' + jumlahCustomer +
                    ' customer';
                sumTot.textContent = 'Total BW ' + fmt(totalBw) + ' | Color ' + fmt(totalColor) + ' | Semua ' + fmt(
                    totalBw + totalColor);
            }

            function terapkan() {
                const kata = cari.value.trim().toLowerCase();
                const tampilList = [];

                document.querySelectorAll('.mesin-card').forEach(function(kartu) {
                    const cocok =
                        (!kata || kartu.dataset.cari.includes(kata)) &&
                        (!rayon.value || kartu.dataset.rayon === rayon.value) &&
                        (!status.value || kartu.dataset.status === status.value) &&
                        (!service.checked || Number(kartu.dataset.service) > 0) &&
                        (!kontrak.checked || kartu.dataset.kontrak === '1');

                    kartu.style.display = cocok ? '' : 'none';
                    if (cocok) tampilList.push(kartu);
                });

                // sembunyikan rayon yang semua kartunya tersaring
                document.querySelectorAll('[data-rayon-box]').forEach(function(box) {
                    const adaTampil = Array.from(box.querySelectorAll('.mesin-card'))
                        .some(function(k) {
                            return k.style.display !== 'none';
                        });
                    box.style.display = adaTampil ? '' : 'none';
                });

                jumlah.textContent = tampilList.length;
                gambarDiagram(tampilList);
            }

            [cari, rayon, status, service, kontrak, periode, top].forEach(function(el) {
                el.addEventListener('input', terapkan);
                el.addEventListener('change', terapkan);
            });

            document.getElementById('f-reset').addEventListener('click', function() {
                cari.value = '';
                rayon.value = '';
                status.value = '';
                service.checked = false;
                kontrak.checked = false;
                periode.value = 'tahun';
                top.value = '0';
                terapkan();
            });

            terapkan();
        })();
    </script>
</body>

</html>
