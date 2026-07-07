<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Rolling DGG</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
        }

        /* Layout Header */
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }

        .logo-area {
            width: 60%;
            text-align: left;
            vertical-align: top;
        }

        .no-sj-area {
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .customer-info {
            margin-bottom: 15px;
        }

        .customer-info strong {
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Unified Table Full Border */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .bg-gray {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }

        .ttd-table td {
            height: 70px;
            vertical-align: top;
            width: 33%;
            border: none;
        }

        .qty-counter {
            text-align: left !important;
            padding-left: 12px !important;
            line-height: 1.5;
            white-space: nowrap;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td class="logo-area">
                <strong style="font-size: 18px;">PT DINAMIKA GLOBAL GEMILANG</strong><br>
                <small>JL. PULASAREN NO 56B. PULASAREN-PEKALIPAN CIREBON </small><br>
                <small>Telp : 0851 8951 5758</small>
            </td>
            <td class="no-sj-area">
                <strong style="font-size: 14px; text-decoration: underline;">SURAT JALAN TUKAR MESIN</strong><br>
                <span>No: {{ $nomor_sj }}</span><br>
                <span>Tanggal: {{ $tanggal }}</span>
            </td>
        </tr>
    </table>

    {{-- DETAIL CUSTOMER: otomatis dari relasi MachineReplacement -> Customer --}}
    <div class="customer-info">
        Kepada Yth:<br>
        <strong>{{ $replacement->customer?->nama_customer ?? '-' }}</strong><br>
        <span>{{ $replacement->customer?->alamat ?? '-' }}</span>
    </div>

    <table class="main-table">
        <thead>
            <tr class="bg-gray">
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Deskripsi Barang / Unit</th>
                <th style="width: 16%;">No Seri/ Kode Part</th>
                <th style="width: 19%;">Type Model/ No Part</th>
                <th style="width: 16%;">Qty / Counter</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- PENARIKAN UNIT LAMA: otomatis dari relasi machine_old --}}
            <tr>
                <td>1</td>
                <td class="text-left" style="font-weight: bold; font-style: italic;">PENARIKAN UNIT (LAMA)</td>
                {{-- <td><strong>{{ $replacement->machine_old?->serial_number ?? '-' }}</strong></td> --}}
                {{-- <td>{{ $replacement->machine_old?->tipe_model ?? '-' }}</td> --}}
                <td>
                    <strong>{{ $replacement->oldMachine?->serial_number ?? '-' }}</strong>
                </td>

                <td>
                    {{ $replacement->oldMachine?->tipe_model ?? ($replacement->oldMachine?->model ?? '-') }}
                </td>
                <td class="qty-counter">
                    BW : {{ number_format($replacement->counter_bw_final ?? 0) }}<br>
                    CL : {{ number_format($replacement->counter_color_final ?? 0) }}
                </td>
                <td class="text-left">{{ $replacement->keterangan ?? '-' }}</td>
            </tr>
            {{-- PENGIRIMAN UNIT BARU: otomatis dari relasi machine_new --}}
            <tr>
                <td>2</td>
                <td class="text-left" style="font-weight: bold; font-style: italic;">PENGIRIMAN UNIT (BARU)</td>
                {{-- <td><strong>{{ $replacement->machine_new?->serial_number ?? '-' }}</strong></td> --}}
                {{-- <td>{{ $replacement->machine_new?->tipe_model ?? '-' }}</td> --}}
                <td>
                    <strong>{{ $replacement->newMachine?->serial_number ?? '-' }}</strong>
                </td>

                <td>
                    {{ $replacement->newMachine?->tipe_model ?? ($replacement->newMachine?->model ?? '-') }}
                </td>
                <td class="qty-counter">
                    BW: {{ number_format($replacement->deployment?->counter_bw ?? 0) }} <br>
                    CL: {{ number_format($replacement->deployment?->counter_color ?? 0) }}
                </td>
                <td class="text-left">-</td>
            </tr>

            {{-- SPAREPART TAMBAHAN: otomatis dari deployment baru hasil rolling, jika ada --}}
            @php $noBaris = 3; @endphp
            @forelse ($replacement->deployment?->deploymentSpareparts ?? [] as $ds)
                <tr>
                    <td>{{ $noBaris++ }}</td>
                    <td class="text-left">
                        {{ $ds->sparepart?->nama_alias ?: $ds->sparepart?->nama_sparepart ?? 'Sparepart' }}</td>
                    <td>
                        {{ $ds->sparepart?->code_part != '0' ? $ds->sparepart?->code_part : '-' }}
                    </td>
                    <td>
                        {{ $ds->sparepart?->no_part != '0' ? $ds->sparepart?->no_part : '-' }}
                    </td>
                    <td>{{ $ds->jumlah }} Pcs</td>
                    <td class="text-left">-</td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                Hormat Kami,<br><br><br><br>
                ( ___________________ )
            </td>
            <td>
                Teknisi Pelaksana,<br><br><br><br>
                ( {{ $replacement->technician?->nama_technician ?? '___________________' }} )
            </td>
            <td>
                Penerima / Customer,<br><br><br><br>
                ( ___________________ )
            </td>
        </tr>
    </table>

</body>

</html>
