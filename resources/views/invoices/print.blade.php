<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Tugas {{ $invoice->invoice_number }}</title>
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
            background: #e5e7eb;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
        }

        .sheet {
            width: 210mm;
            height: 148mm;
            margin: 8mm auto;
            padding: 7mm;
            background: #fff;
            position: relative;
        }

        .frame {
            border: 1px solid #000;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 4mm 5mm 3mm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .doc-title {
            font-size: 20pt;
            font-weight: 800;
            letter-spacing: .5px;
        }

        .doc-no {
            font-size: 11pt;
            margin-top: 1mm;
        }

        .company {
            text-align: right;
        }

        .company-name {
            font-size: 13pt;
            font-weight: 800;
        }

        .company-addr {
            font-size: 9pt;
            font-weight: 700;
            margin-top: .5mm;
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin-top: 4mm;
        }

        .box {
            border: 1px solid #000;
            padding: 2mm 3mm;
            width: 98mm;
            line-height: 1.45;
        }

        .meta {
            width: 85mm;
            line-height: 1.5;
        }

        .meta table {
            border-collapse: collapse;
        }

        .meta td {
            padding: 0;
            vertical-align: top;
        }

        .meta td:first-child {
            width: 26mm;
        }

        .meta td:nth-child(2) {
            width: 5mm;
        }

        .meta .ref {
            margin-top: 1.5mm;
            font-size: 9pt;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 1.5mm 2mm;
            text-align: left;
        }

        table.items th {
            font-weight: 700;
        }

        table.items tbody td {
            height: 25mm;
            vertical-align: bottom;
        }

        .rp {
            display: flex;
            justify-content: space-between;
        }

        .closing {
            margin-top: 2.5mm;
            font-size: 9.5pt;
        }

        .sign {
            display: flex;
            justify-content: space-between;
            margin-top: 2mm;
            flex: 1;
        }

        .sign>div {
            width: 70mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Ubah ke flex-start agar nama sejajar ke atas */
        }

        .sign .signature-area {
            margin-top: 15mm;
            /* Ruang untuk tanda tangan */
            text-align: center;
        }

        .sign .line {
            border-top: 1px solid #000;
        }

        .sign .right {
            text-align: left;
        }

        .stamp {
            position: absolute;
            left: 7mm;
            bottom: 2.2mm;
            font-size: 7pt;
            color: #333;
        }

        @media print {

            html,
            body {
                background: #fff;
            }

            .sheet {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="frame">
            <div class="header">
                <div>
                    <div class="doc-title">SURAT TUGAS</div>
                    <div class="doc-no">{{ $invoice->invoice_number }}</div>
                </div>
                <div class="company">
                    <div>
                        <div class="company-name">PT DINAMIKA GLOBAL GEMILANG</div>
                        <div class="company-addr">JL. GURAME 20 - BANDUNG</div>
                    </div>
                </div>
            </div>

            <div class="info">
                <div class="box">
                    <div>Kepada Yth</div>
                    <!-- Mengambil nama customer dari relasi -->
                    <div style="font-weight: bold;">
                        {{ $invoice->customer->nama_customer ?? 'Nama Customer Tidak Ditemukan' }}</div>

                    <!-- Asumsi model Customer memiliki field alamat dan telepon. Jika tidak ada, bisa dihapus atau disesuaikan -->
                    <div>{{ $invoice->customer->alamat ?? 'Alamat Customer' }}</div>
                    <div>{{ $invoice->customer->no_telepon ?? '' }}</div>
                </div>
                <div class="meta">
                    <table>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td>No. Pelanggan</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->kode_pelanggan ?? '-' }}</td>
                        </tr>
                        <!-- Tambahkan field lain jika perlu -->
                    </table>
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th style="width:24mm">Tanggal</th>
                        <th style="width:34mm">No.Faktur</th>
                        <th>No.Pajak</th>
                        <th style="width:36mm">Total</th>
                        <th style="width:38mm">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td></td>
                        <td>
                            <div class="rp">
                                <span>Rp</span>
                                <span>{{ number_format($invoice->nominal, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="closing">Demikianlah surat tugas ini diberikan guna keperluan penagihan</div>

            <div class="sign">
                <div>
                    <div>PT DINAMIKA GLOBAL GEMILANG</div>
                    <div class="signature-area">
                        <div class="line"></div>
                        <!-- Bisa diisi dengan nama perusahaan / pengirim -->
                        <div style="font-weight: bold; margin-top: 2mm; text-align: left;">
                            {{ $invoice->nama_pengirim ?? '( Admin )' }}
                        </div>
                    </div>
                </div>
                <div class="right">
                    <!-- Tanggal diterima -->
                    <div>Diterima,
                        {{ $invoice->received_at ? \Carbon\Carbon::parse($invoice->received_at)->format('d/m/Y') : '____/____/____' }}
                    </div>
                    <div class="signature-area">
                        <div class="line"></div>
                        <!-- MENAMPILKAN NAMA PENERIMA -->
                        <div style="font-weight: bold; margin-top: 2mm; text-align: left;">
                            {{ $invoice->nama_penerima ?? '( ...................................... )' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Stamp bawah -->
        <div class="stamp">
            {{ $invoice->nama_pengirim ?? 'ADMIN' }} &nbsp;
            {{ now()->format('d-m-Y H:i:s') }}
        </div>
    </div>

    <!-- Script untuk otomatis print saat halaman dibuka -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
