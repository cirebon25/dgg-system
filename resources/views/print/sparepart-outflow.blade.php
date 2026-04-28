<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pengeluaran Sparepart DGG</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: sans-serif; font-size: 10px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background: #f2f2f2; text-align: center; }
        .header { text-align: center; margin-bottom: 15px; }
        .total-row { font-weight: bold; background: #eee; }
        .text-blue { color: #3b82f6; font-weight: bold; }
        .text-red { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0">REKAP PENGELUARAN SPAREPART - DGG SYSTEM</h2>
        <p style="margin:5px">Periode: {{ $month }} / {{ $year }}</p>
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
            @php $total = 0; @endphp
            @forelse($usages as $index => $usage)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($usage->serviceLog->tanggal)->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $usage->sparepart->nama_sparepart }}</strong><br>
                    <small>{{ $usage->sparepart->code_part ?? '-' }} / {{ $usage->sparepart->no_part ?? '-' }}</small>
                </td>
                <td style="text-align:center">{{ $usage->jumlah }}</td>
                <td>
                    <strong>{{ $usage->serviceLog->machine->deployment->customer->nama_customer ?? 'Umum' }}</strong><br>
                    <small>SN: {{ $usage->serviceLog->machine->serial_number }}</small>
                </td>
                
                <td>
                    BW: {{ number_format($usage->serviceLog->counter_bw) }}<br>
                    CL: {{ number_format($usage->serviceLog->counter_color) }}
                </td>

                <td>
                    <span class="text-blue">BW: {{ number_format($usage->serviceLog->usage_bw) }}</span><br>
                    <span class="text-red">CL: {{ number_format($usage->serviceLog->usage_color) }}</span>
                </td>

                <td>{{ $usage->serviceLog->perbaikan }}</td>
                <td>{{ $usage->serviceLog->technician->nama_technician ?? '-' }}</td>
            </tr>
            @php $total += $usage->jumlah; @endphp
            @empty
            <tr>
                <td colspan="9" style="text-align:center">Tidak ada data pengeluaran.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align:right">TOTAL BARANG KELUAR :</td>
                <td style="text-align:center">{{ $total }}</td>
                <td colspan="5"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>