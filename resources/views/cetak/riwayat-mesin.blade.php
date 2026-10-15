@php
    $f = fn($v) => number_format((float) $v, 0, ',', '.');
    $adaTagihan = $kontrak && $kontrak->sewa !== null;
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
        th.tagihan {
            background: #548235 !important;
            border-color: #548235 !important;
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

    {{-- ===== Toolbar + Filter Tahun ===== --}}
    <form method="GET" class="toolbar no-print">
        <label for="tahun">Tahun:</label>
        <select name="tahun" id="tahun" onchange="this.form.submit()">
            <option value="semua" @selected(!$tahunDipilih)>Semua Tahun</option>
            @foreach ($daftarTahun as $t)
                <option value="{{ $t }}" @selected($tahunDipilih === $t)>{{ $t }}</option>
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

    {{-- ===== 2. PEMAKAIAN PER BULAN + TAGIHAN ===== --}}
    <div class="sub">
        PEMAKAIAN PER BULAN @if ($adaTagihan)
            &amp; TAGIHAN
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

    <table class="data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th style="width:55px">Kunjungan</th>
                <th>Pemakaian BW</th>
                @if ($adaColor)
                    <th>Pemakaian Color</th>
                @endif
                @if ($adaTagihan)
                    <th class="tagihan">Free Copy{{ $adaColor ? ' (BW/Color)' : '' }}</th>
                    <th class="tagihan">Kelebihan{{ $adaColor ? ' (BW/Color)' : '' }}</th>
                    <th class="tagihan">Harga Sewa</th>
                    <th class="tagihan">Biaya Kelebihan</th>
                    <th class="tagihan">Subtotal</th>
                    <th class="tagihan">PPN {{ rtrim(rtrim(number_format($kontrak->ppn, 2, ',', '.'), '0'), ',') }}%
                    </th>
                    <th class="tagihan">Total Tagihan</th>
                @endif
                <th style="width:70px">Ket.</th>
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
                            <td colspan="7" class="center">-</td>
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
        Parsial = bulan belum terhitung penuh, tagihan masih sementara.
        @if ($anomali > 0)
            <span style="color:#c00000">⚠ {{ $anomali }} pembacaan counter mundur dilewati (kemungkinan salah
                input).</span>
        @endif
    </div>

    {{-- ===== 3. RIWAYAT PENGGANTIAN PART + TOTAL ===== --}}
    <div class="sub">
        RIWAYAT PENGGANTIAN PART
        <small>(selisih = kenaikan counter sejak penggantian part yang sama sebelumnya)</small>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:75px">Tanggal</th>
                <th>Nama Part</th>
                <th style="width:40px">Qty</th>
                <th>Counter BW</th>
                <th>Selisih BW</th>
                @if ($adaColorPart)
                    <th>Counter Color</th>
                    <th>Selisih Color</th>
                @endif
                <th style="width:70px">Selisih Hari</th>
                <th style="width:90px">Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gantiTahun as $i => $r)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td class="center">{{ $r->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $r->part }}</td>
                    <td class="center">{{ $r->qty }}</td>
                    <td class="angka">{{ $r->bw ? $f($r->bw) : '-' }}</td>
                    <td class="tebal {{ $r->selisih_bw !== null && $r->selisih_bw < 0 ? 'minus' : '' }}">
                        @if ($r->selisih_bw === null)
                            -
                        @elseif ($r->selisih_bw < 0)
                            ⚠ {{ $f($r->selisih_bw) }}
                        @else
                            +{{ $f($r->selisih_bw) }}
                        @endif
                    </td>
                    @if ($adaColorPart)
                        <td class="angka">{{ $r->color ? $f($r->color) : '-' }}</td>
                        <td class="tebal {{ $r->selisih_clr !== null && $r->selisih_clr < 0 ? 'minus' : '' }}">
                            @if ($r->selisih_clr === null)
                                -
                            @elseif ($r->selisih_clr < 0)
                                ⚠ {{ $f($r->selisih_clr) }}
                            @else
                                +{{ $f($r->selisih_clr) }}
                            @endif
                        </td>
                    @endif
                    <td class="center">{{ $r->hari !== null ? $r->hari . ' hari' : '-' }}</td>
                    <td>{{ $r->teknisi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $adaColorPart ? 10 : 8 }}" class="center">Tidak ada penggantian part pada
                        {{ $periodeLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($totalPart->isNotEmpty())
        <div class="sub">TOTAL PART YANG SUDAH DIGANTI <small>({{ $periodeLabel }})</small></div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width:30px">No</th>
                    <th>Nama Part</th>
                    <th style="width:90px">Total Qty</th>
                    <th style="width:90px">Kali Diganti</th>
                    <th style="width:110px">Terakhir Diganti</th>
                    <th style="width:150px">Rata-rata Selisih BW</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($totalPart as $i => $p)
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
                    <td class="center">{{ $f($grandPart) }}</td>
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
                <th style="width:70px">Tanggal</th>
                <th style="width:100px">Event</th>
                <th style="width:85px">Tipe Kunjungan</th>
                <th style="width:150px">Customer</th>
                <th style="width:100px">Teknisi</th>
                <th>Detail</th>
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
</body>

</html>
