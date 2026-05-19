<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pengeluaran Sparepart DGG</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: sans-serif; font-size: 10px; padding: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: middle; }
        th {
            background: #facc15 !important;
            color: #000 !important;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .rayon-group-title {
            background-color: #facc15 !important;
            color: #000 !important;
            padding: 10px 12px;
            margin-top: 10px;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #000;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .total-row { font-weight: bold; background: #eee; }
        .text-blue { color: #3b82f6; font-weight: bold; }
        .text-red  { color: #ef4444; font-weight: bold; }
        .text-center { text-align: center; }
        .page-break { page-break-before: always; break-before: page; }
        .empty-box {
            text-align: center;
            padding: 40px;
            border: 1px solid #000;
            font-weight: bold;
            background-color: #fee2e2;
            color: #991b1b;
            margin-top: 30px;
        }
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

@if($groupedUsages->isEmpty())

    <div class="header">
        <h2 style="margin:0">REKAP PENGELUARAN SPAREPART - DGG SYSTEM</h2>
        <p style="margin:5px; font-weight:bold;">Periode: {{ $month }} / {{ $year }}</p>
    </div>
    <div class="empty-box">
        ⚠️ Tidak ada data transaksi pengeluaran sparepart pada periode {{ $month }} / {{ $year }}.
    </div>

@else

    @foreach($groupedUsages as $namaRayon => $itemsKeluar)

        <div class="{{ $loop->first ? '' : 'page-break' }}">

            <div class="header">
                <h2 style="margin:0">REKAP PENGELUARAN SPAREPART - DGG SYSTEM</h2>
                <p style="margin:5px; font-weight:bold;">Periode: {{ $month }} / {{ $year }}</p>
            </div>

            <div class="rayon-group-title">
                📍 WILAYAH / RAYON: {{ $namaRayon }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Tgl Keluar</th>
                        <th width="15%">Nama Barang / Part No</th>
                        <th width="3%">Qty</th>
                        <th width="15%">Customer & SN Mesin</th>
                        <th width="10%">Counter (BW/CL)</th>
                        <th width="10%">Usage (BW/CL)</th>
                        <th width="15%">Perbaikan / Tindakan</th>
                        <th width="10%">Teknisi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subTotal = 0; @endphp

                    @forelse($itemsKeluar as $index => $usage)
                        @php
                            $log      = $usage->serviceLog;
                            $part     = $usage->sparepart;
                            $tech     = optional($log)->technician;
                            $machine  = optional($log)->machine;
                            $customer = optional(optional($machine)->deployment)->customer;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>

                            <td>
                                {{ $log && $log->tanggal
                                    ? \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td>
                                <strong>{{ optional($part)->nama_sparepart ?? '-' }}</strong><br>
                                <small>
                                    {{ optional($part)->code_part ?? '-' }} /
                                    {{ optional($part)->no_part   ?? '-' }}
                                </small>
                            </td>

                            <td class="text-center">
                                <b>{{ $usage->jumlah }}</b>
                            </td>

                            <td>
                                <strong>{{ optional($customer)->nama_customer ?? 'Umum' }}</strong><br>
                                <small>SN: {{ optional($machine)->serial_number ?? '-' }}</small>
                            </td>

                            <td>
                                BW: {{ number_format(optional($log)->counter_bw ?? 0) }}<br>
                                CL: {{ number_format(optional($log)->counter_color ?? 0) }}
                            </td>

                            <td>
                                <span class="text-blue">
                                    BW: {{ number_format(optional($log)->usage_bw ?? 0) }}
                                </span><br>
                                <span class="text-red">
                                    CL: {{ number_format(optional($log)->usage_color ?? 0) }}
                                </span>
                            </td>

                            <td>{{ optional($log)->perbaikan ?? '-' }}</td>

                            <td>{{ optional($tech)->nama_technician ?? '-' }}</td>
                        </tr>
                        @php $subTotal += $usage->jumlah; @endphp

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                Tidak ada data untuk rayon ini.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right; font-weight:bold;">
                            TOTAL BARANG KELUAR RAYON {{ $namaRayon }} :
                        </td>
                        <td class="text-center"
                            style="background-color:#bbf7d0; color:#166534; font-weight:bold;">
                            {{ $subTotal }}
                        </td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top:15px; float:right; text-align:center; width:220px;">
                <p>Cirebon, {{ date('d-m-Y') }}</p>
                <br><br><br>
                <p><b>( _________________ )</b></p>
                <p>Admin Gudang Pusat</p>
            </div>
            <div style="clear:both;"></div>

        </div>

    @endforeach

@endif

</body>
</html>