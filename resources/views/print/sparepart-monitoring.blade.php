<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Monitoring Umur Sparepart - {{ $machine->serial_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        /* Header Laporan */
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 8px 0;
            color: #1a202c;
            font-size: 18px;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 4px 0;
            color: #4a5568;
            font-size: 12px;
        }

        /* Container Setiap Kelompok Part */
        .part-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        /* Judul/Header Kotak Part */
        .part-title {
            background-color: #2d3748;
            color: #ffffff;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0.3px;
            border-radius: 4px 4px 0 0;
        }

        /* Styling Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #cbd5e0;
            padding: 7px 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Efek Zebra Baris Tabel */
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tbody tr:hover {
            background-color: #edf2f7;
        }

        /* Utility Classes */
        .text-danger {
            color: #e53e3e;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Pengaturan Khusus Print / Cetak PDF */
        @media print {
            body {
                padding: 0;
                background-color: #fff;
            }

            .part-title {
                background-color: #2d3748 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            th {
                background-color: #edf2f7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h2>MONITORING UMUR PAKAI SPAREPART</h2>
        <p>
            <strong>Customer:</strong> {{ $machine->deployment->customer->nama_customer ?? '-' }} &nbsp;|&nbsp;
            <strong>SN:</strong> {{ $machine->serial_number }} &nbsp;|&nbsp;
            <strong>Model:</strong> {{ $machine->model_mesin ?? '-' }}
        </p>
        <p>
            <strong>Counter Mesin Saat Ini:</strong>
            BW: {{ number_format($counterSekarangBW) }} &nbsp;|&nbsp;
            Color: {{ number_format($counterSekarangCL) }}
        </p>
    </div>

    @forelse ($partHistories as $sparepartId => $histories)
        @php
            $sampleSparepart = $histories->first()->sparepart;
            $namaSparepart = $sampleSparepart->nama_sparepart ?? 'Sparepart Tidak Dikenal';
            $targetUmur = $sampleSparepart->target_umur ?? 150000;
            $isColor = isset($sampleSparepart->tipe_counter) && strtolower($sampleSparepart->tipe_counter) == 'cl';
        @endphp

        <div class="part-section">
            <div class="part-title">
                📦 {{ $namaSparepart }} &nbsp;&mdash;&nbsp; Target Umur: {{ number_format($targetUmur) }} Klik
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="13%">Tanggal Pasang</th>
                        <th width="6%">Qty</th>
                        <th width="12%">Counter Awal</th>
                        <th width="12%">Counter Akhir</th>
                        <th width="14%">Total Pemakaian</th>
                        <th width="12%">Sisa Umur</th>
                        <th>Lokasi / Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $index => $history)
                        @php
                            $serviceLog = optional($history->serviceLog);
                            $awal = $isColor ? $serviceLog->counter_color : $serviceLog->counter_bw;
                            $awal = $awal ?? 0;

                            $qty = $history->jumlah ?? ($history->qty ?? 1);

                            // Cari counter akhir dari riwayat part yang SAMA berikutnya di collection ini
                            $counterAkhir = null;
                            $historiesArray = $histories->values();
                            if (isset($historiesArray[$index + 1])) {
                                $nextLog = optional($historiesArray[$index + 1]->serviceLog);
                                $counterAkhir = $isColor ? $nextLog->counter_color : $nextLog->counter_bw;
                            }

                            // Jika tidak ada pengganti setelahnya, pakai counter mesin saat ini
                            if ($counterAkhir === null) {
                                $counterAkhir = $isColor ? $counterSekarangCL : $counterSekarangBW;
                            }

                            $pakai = max(0, $counterAkhir - $awal);

                            $sisaPersen = 0;
                            if ($targetUmur > 0) {
                                $sisa = $targetUmur - $pakai;
                                $sisaPersen = max(0, min(100, round(($sisa / $targetUmur) * 100)));
                            }

                            // Format tanggal aman
                            $rawTanggal = $serviceLog->tanggal;
                            $formattedTanggal = '-';
                            if ($rawTanggal) {
                                try {
                                    $formattedTanggal = \Carbon\Carbon::parse($rawTanggal)->format('d/m/Y');
                                } catch (\Exception $e) {
                                    $formattedTanggal = '-';
                                }
                            }

                            // Nama customer aman
                            $customerName =
                                optional(optional(optional($serviceLog->machine)->deployment)->customer)
                                    ->nama_customer ?? 'Workshop/Internal';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $formattedTanggal }}</td>
                            <td class="text-center">{{ $qty }}</td>
                            <td class="text-right">{{ number_format($awal) }}</td>
                            <td class="text-right">{{ number_format($counterAkhir) }}</td>
                            <td class="text-right"><strong>{{ number_format($pakai) }}</strong> Lembar</td>
                            <td class="text-center {{ $sisaPersen < 15 ? 'text-danger' : '' }}">
                                {{ $sisaPersen }}%
                                @if ($pakai >= $targetUmur)
                                    <br><span style="font-size: 9px; color: red;">(Habis)</span>
                                @endif
                            </td>
                            <td>{{ $customerName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p class="text-center" style="padding: 30px; color: #718096;">Belum ada riwayat penggantian sparepart untuk
            mesin ini.</p>
    @endforelse
</body>

</html>
