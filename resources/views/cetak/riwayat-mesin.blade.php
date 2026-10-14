<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Mesin {{ $machine->serial_number }}</title>
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
        }

        .toolbar button {
            padding: 7px 16px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #333;
            background: #fff;
            border-radius: 4px;
        }

        .judul {
            background: #1f4e79;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            padding: 7px 10px;
        }

        table.info {
            border-collapse: collapse;
            margin: 10px 0 14px;
            width: 100%;
        }

        table.info th {
            background: #dbe5f1;
            border: 1px solid #8ea9c8;
            padding: 4px 8px;
            font-size: 10px;
            text-align: left;
            width: 16%;
        }

        table.info td {
            border: 1px solid #8ea9c8;
            padding: 4px 8px;
            font-weight: bold;
        }

        .sub {
            font-weight: bold;
            margin: 14px 0 4px;
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
        }

        table.data td {
            padding: 4px 6px;
            border-bottom: 1px solid #d9d9d9;
            vertical-align: top;
        }

        tr.siklus td {
            background: #fff2cc;
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #bf9000;
        }

        /* ===== Penanda tanggal kembar (tiap tanggal beda warna) ===== */
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

        .legenda {
            font-size: 10px;
            margin: 6px 0 8px;
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

<body onload="window.print()">

    <div class="toolbar no-print">
        <button onclick="window.print()">🖨 Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="judul">RIWAYAT MESIN — NS: {{ $machine->serial_number }}</div>

    <table class="info">
        <tr>
            <th>No Seri</th>
            <td>{{ $machine->serial_number }}</td>
            <th>Customer Sekarang</th>
            <td>{{ $machine->customer->nama_customer ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status Terakhir</th>
            <td>{{ $statusAkhir }}</td>
            <th>Tercatat Sejak</th>
            <td>{{ $tanggalAwal ? $tanggalAwal->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Total Service</th>
            <td>{{ $totalService }}x</td>
            <th>Total Event</th>
            <td>{{ $events->count() }}</td>
        </tr>
    </table>

    @if ($partDiganti->isNotEmpty())
        <div class="sub">PART YANG PERNAH DIGANTI</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Nama Part</th>
                    <th style="width:80px">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($partDiganti as $p)
                    <tr>
                        <td>{{ $p->nama }}</td>
                        <td class="center">{{ number_format($p->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="sub">RIWAYAT KEJADIAN</div>

    @if ($jumlahKembar > 0 || $jumlahDobel > 0)
        <div class="legenda">
            @if ($jumlahKembar > 0)
                <span class="kotak"></span>
                Baris berwarna = ada lebih dari 1 event pada tanggal yang sama
                ({{ $jumlahKembar }} tanggal).
            @endif
            @if ($jumlahDobel > 0)
                &nbsp;&nbsp;
                <strong style="color:#c00000">⚠</strong>
                = Service dengan isi persis sama pada tanggal yang sama, kemungkinan input dobel
                ({{ $jumlahDobel }} kasus).
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
                        <td colspan="6">
                            {{ $e->siklus === 0 ? 'AWAL / STOK' : 'SIKLUS KE-' . $e->siklus }}
                        </td>
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

                        @if ($e->counter)
                            <span class="kecil">Counter: {{ number_format($e->counter) }}</span>
                        @endif

                        @if (!empty($e->dobel))
                            <span class="dobel">⚠ kemungkinan dobel</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Belum ada riwayat untuk nomor seri ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="tgl-cetak">Dicetak: {{ now()->format('d-m-Y H:i') }}</div>
</body>

</html>
