<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pengeluaran Sparepart DGG Per Rayon</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: sans-serif; font-size: 10px; color: #333; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 25px; }
        td { border: 1px solid black; padding: 6px 5px; vertical-align: middle; }
        
        /* 🌟 STYLING HEADER WILAYAH KUNING EMAS */
        .rayon-group-title { 
            background-color: #facc15 !important; 
            color: #000000 !important;
            padding: 10px 12px; 
            margin-top: 10px;
            font-size: 13px; 
            font-weight: bold; 
            border: 1px solid #000;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important;
        }

        /* 🌟 FONT HEADER TABEL HITAM PEKAT */
        th { 
            border: 1px solid black; 
            background-color: #f2f2f2 !important; 
            text-align: center; 
            color: #000000 !important; 
            font-weight: bold; 
            padding: 8px 5px; 
            text-transform: uppercase; 
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .total-row { font-weight: bold; background: #eee; color: #000000; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* 🌟 POTONG KERTAS OTOMATIS: 1 RAYON 1 LEMBAR */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } 
        }
    </style>
</head>
<body onload="window.print()">

    {{-- 🌟 MANTRA GROUPING BLADE ANTI-ERROR: Kelompokkan data aman dengan fungsi pelindung optional() --}}
    @php
        $groupedUsages = collect($usages)->groupBy(function($item) {
            return optional(optional(optional($item->serviceLog)->technician)->rayon)->nama_rayon 
                   ?? 'BARAT DAYA'; // Jika data kosong/null, paksa amankan ke kelompok Barat Daya
        });
    @endphp

    @forelse($groupedUsages as $namaRayon => $itemsKeluar)
        {{-- Pembatas halaman otomatis: jika bukan rayon pertama, printer otomatis pindah kertas baru --}}
        <div class="{{ $loop->first ? '' : 'page-break' }}">
            
            <div class="header">
                <h2 style="margin:0; color: #000;">REKAP PENGELUARAN SPAREPART - DGG SYSTEM</h2>
                <p style="margin:5px; font-weight: bold;">Periode Laporan: {{ $month ?? date('m') }} / {{ $year ?? date('Y') }}</p>
            </div>

            <div class="rayon-group-title">📍 WILAYAH / RAYON: {{ $namaRayon }}</div>

            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Tgl</th>
                        <th width="18%">Nama Customer</th>
                        <th width="10%">Tipe Model</th>
                        <th width="11%">No Seri</th>
                        <th width="18%">Sparepart</th>
                        <th width="11%">Pemakaian 1 Bulan Terakhir</th>
                        <th width="11%">Counter Akhir</th>
                        <th width="10%">Teknisi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subTotalQty = 0; @endphp
                    @foreach($itemsKeluar as $index => $usage)
                        @php
                            // Mengunci variabel agar serasi dengan bungkusan objek lama Akang
                            $tanggalRaw = optional($usage->serviceLog)->tanggal ?? date('Y-m-d');
                            $customerName = optional(optional(optional(optional($usage->serviceLog)->machine)->deployment)->customer)->nama_customer ?? 'Umum';
                            $tipeModel = optional(optional($usage->serviceLog)->machine)->tipe_model ?? '-';
                            $serialNumber = optional(optional($usage->serviceLog)->machine)->serial_number ?? '-';
                            $namaSparepart = optional($usage->sparepart)->nama_sparepart ?? '-';
                            $codePart = optional($usage->sparepart)->code_part ?? '-';
                            $jumlahBarang = $usage->jumlah ?? 0;
                            
                            $usageBW = optional($usage->serviceLog)->usage_bw ?? 0;
                            $usageCL = optional($usage->serviceLog)->usage_color ?? 0;
                            $counterBW = optional($usage->serviceLog)->counter_bw ?? 0;
                            $counterCL = optional($usage->serviceLog)->counter_color ?? 0;
                            $namaTeknisi = optional(optional($usage->serviceLog)->technician)->nama_technician ?? '-';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($tanggalRaw)->format('d/m/Y') }}</td>
                            <td><strong>{{ $customerName }}</strong></td>
                            <td>{{ $tipeModel }}</td>
                            <td class="font-bold">{{ $serialNumber }}</td>
                            <td>
                                <strong>{{ $namaSparepart }}</strong> <span style="color: #2563eb;">({{ $jumlahBarang }} Pcs)</span><br>
                                <small style="color: #666;">Code: {{ $codePart }}</small>
                            </td>
                            
                            {{-- Pemakaian 1 Bulan Terakhir --}}
                            <td>
                                BW: {{ number_format($usageBW) }}<br>
                                CL: {{ number_format($usageCL) }}
                            </td>

                            {{-- Counter Akhir --}}
                            <td style="background-color: #f8fafc;">
                                BW: {{ number_format($counterBW) }}<br>
                                CL: {{ number_format($counterCL) }}
                            </td>

                            <td>{{ $namaTeknisi }}</td>
                        </tr>
                        @php $subTotalQty += $jumlahBarang; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" style="text-align:right; padding: 8px;">TOTAL SPAREPART KELUAR RAYON {{ $namaRayon }} :</td>
                        <td style="background-color: #bbf7d0; color: #166534; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;" class="text-center">{{ $subTotalQty }} Pcs</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 15px; float: right; text-align: center; width: 220px;">
                <p>Cirebon, {{ date('d-m-Y') }}</p>
                <br><br><br>
                <p><b>( _________________ )</b></p>
                <p>Admin Gudang Pusat</p>
            </div>
            <div style="clear: both;"></div>

        </div>
    @empty
        <div style="text-align:center; padding: 50px; border: 1px solid #000; font-weight: bold; background-color: #fee2e2; color: #991b1b;">
            Belum ada data transaksi pengeluaran sparepart untuk periode ini.
        </div>
    @endforelse

</body>
</html>