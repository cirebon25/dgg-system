<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Part Keluar {{ $bulan }} {{ $tahun }}</title>
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
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar button,
        .toolbar select {
            padding: 7px 12px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #333;
            background: #fff;
            border-radius: 4px;
        }

        .toolbar select {
            background: #f9f9f9;
            min-width: 220px;
        }

        .toolbar button:hover {
            background: #eee;
        }

        .judul {
            background: #1f4e79;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            padding: 6px 8px;
        }

        .part-block {
            margin-bottom: 25px;
        }

        table.info {
            border-collapse: collapse;
            margin: 0 0 14px;
        }

        table.info th {
            background: #dbe5f1;
            border: 1px solid #8ea9c8;
            padding: 3px 8px;
            font-size: 10px;
        }

        table.info td {
            border: 1px solid #8ea9c8;
            padding: 4px 8px;
            font-weight: bold;
            text-align: center;
        }

        table.info td.part {
            background: #fff2cc;
            text-align: left;
            min-width: 280px;
        }

        table.info td.total {
            background: #dbe5f1;
        }

        .kolom {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .kolom .a {
            width: 28%;
        }

        .kolom .b {
            width: 28%;
        }

        .kolom .c {
            width: 44%;
        }

        .sub {
            font-weight: bold;
            font-size: 11px;
            margin: 0 0 4px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #1f4e79;
            color: #fff;
            padding: 4px 6px;
            font-size: 10px;
            text-align: center;
        }

        table.data td {
            padding: 3px 6px;
            border-bottom: 1px solid #d9d9d9;
        }

        table.data tfoot td {
            border-top: 2px solid #1f4e79;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .ns {
            display: block;
            font-size: 9px;
            color: #555;
        }

        .tgl-cetak {
            margin-top: 12px;
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

            .part-block {
                page-break-after: always;
            }

            .part-block:last-of-type {
                page-break-after: auto;
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

    <div class="toolbar no-print">
        <button onclick="window.print()">🖨 Cetak</button>

        <!-- Dropdown Pilih Part Langsung di atas -->
        <select id="gantiPart" onchange="gantiPartFilter(this.value)">
            <option value="">-- Pilih / Ganti Part --</option>
            @foreach ($semuaPart ?? [] as $sp)
                <option value="{{ $sp->id }}" {{ request('sparepart_ids.0') == $sp->id ? 'selected' : '' }}>
                    {{ $sp->nama_sparepart }}
                </option>
            @endforeach
        </select>

        <button onclick="window.close()">Tutup</button>
    </div>

    <script>
        function gantiPartFilter(partId) {
            if (!partId) return;
            let url = new URL(window.location.href);
            url.searchParams.set('sparepart_ids[0]', partId);
            window.location.href = url.toString();
        }
    </script>

    <div class="judul">
        PENCARIAN PART KELUAR
        @if (!empty($filterPart))
            &nbsp;|&nbsp; Part: {{ $filterPart }}
        @endif
        @if (!empty($filterCustomer))
            &nbsp;|&nbsp; Customer: {{ $filterCustomer }}
        @endif
    </div>
    <div style="height:10px"></div>

    @forelse ($perPart as $p)
        <div class="part-block">

            <table class="info">
                <tr>
                    <th>Nama Part</th>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Total Qty Keluar</th>
                </tr>
                <tr>
                    <td class="part">{{ $p->sparepart->nama_sparepart ?? '-' }}</td>
                    <td>{{ $tahun }}</td>
                    <td>{{ $bulan }}</td>
                    <td class="total">{{ number_format($p->total) }}</td>
                </tr>
            </table>

            <div class="kolom">

                {{-- A. Keluar ke customer --}}
                <div class="a">
                    <div class="sub">A. KELUAR KE CUSTOMER</div>
                    <table class="data">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th style="width:45px">Qty</th>
                                <th style="width:55px">Trans</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($p->ringkas as $r)
                                <tr>
                                    <td>{{ $r->customer }}</td>
                                    <td class="right">{{ number_format($r->total) }}</td>
                                    <td class="right">{{ $r->transaksi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($p->ringkas->count() > 1)
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td class="right">{{ number_format($p->total) }}</td>
                                    <td class="right">{{ $p->ringkas->sum('transaksi') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- B. Keluar per Tanggal --}}
                <div class="b">
                    <div class="sub">B. KELUAR PER TANGGAL</div>
                    <table class="data">
                        <thead>
                            <tr>
                                <th style="width:75px">Tanggal</th>
                                <th style="width:45px">Qty</th>
                                <th style="width:55px">Trans</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($p->ringkas_tanggal as $d)
                                <tr>
                                    <td class="center">
                                        {{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="right">{{ number_format($d->total) }}</td>
                                    <td class="right">{{ $d->transaksi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($p->ringkas_tanggal->count() > 1)
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td class="right">{{ number_format($p->total) }}</td>
                                    <td class="right">{{ $p->ringkas_tanggal->sum('transaksi') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- C. Rincian transaksi --}}
                <div class="c">
                    <div class="sub">C. RINCIAN TRANSAKSI</div>
                    <table class="data">
                        <thead>
                            <tr>
                                <th style="width:75px">Tanggal</th>
                                <th>Customer</th>
                                <th style="width:45px">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($p->transaksi as $t)
                                <tr>
                                    <td class="center">
                                        {{ $t->tanggal ? \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        {{ $t->customer }}
                                        <span class="ns">NS: {{ $t->sn }}</span>
                                    </td>
                                    <td class="right">{{ number_format($t->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @empty
        <p style="text-align:center; margin-top:30px;">
            Tidak ada part keluar pada {{ $bulan }} {{ $tahun }}.
        </p>
    @endforelse

    <div class="tgl-cetak">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>

</body>

</html>
