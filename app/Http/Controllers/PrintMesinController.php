<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StokGudangExport;
use Maatwebsite\Excel\Facades\Excel;


class PrintMesinController extends Controller
{
    // dipindah dari: Route::get('/cetak-alokasi-customer', ...)->name('cetak.alokasi-customer')
    public function alokasiCustomer()
    {
        $data = Deployment::with(['customer', 'machine'])->get();

        $html = "
        <html>
        <head>
            <title>Laporan Alokasi Customer</title>
            <style>
                body { font-family: sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                th { background: #e5e7eb; }
            </style>
        </head>
        <body onload='window.print()'>
            <h2 style='text-align:center'>DAFTAR ALOKASI UNIT CUSTOMER</h2>
            <table>
                <thead>
                    <tr>                    
                        <th>NAMA CUSTOMER</th>
                        <th>SN MESIN</th>
                        <th>TGL PASANG</th>
                        <th>HARGA SEWA</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($data as $d) {
            $html .= "<tr>
                <td>" . ($d->customer->nama_customer ?? '-') . "</td>
                <td>" . ($d->machine->serial_number ?? '-') . "</td>
                <td>" . ($d->tanggal_pasang ?? '-') . "</td>
                <td>Rp " . number_format($d->harga_sewa, 0, ',', '.') . "</td>
            </tr>";
        }

        $html .= "</tbody></table></body></html>";

        return response($html);
    }

    // dipindah dari: Route::get('/mesin/{id}/cetak-qr', ...)->name('mesin.cetak-qr')
    public function cetakQr($id)
    {
        try {
            $machine = Machine::findOrFail($id);
        } catch (\Exception $e) {
            return 'Data mesin tidak ditemukan!';
        }

        $urlHistori = route('mesin.histori', ['id' => $machine->id]);

        $qrCode = QrCode::size(100)->margin(0)->generate($urlHistori);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Mesin - {$machine->serial_number}</title>
            <style>
                /* 🛠️ SETTING UKURAN STIKER THERMAL (50mm x 40mm) */
    @page{
                    size: 50mm 40mm; 
                    margin: 0; 
                } 
                
                body { 
                    font-family: Arial, sans-serif; 
                    text-align: center; 
                    margin: 0; 
                    padding: 5px; 
                    width: 50mm; 
                    height: 40mm; 
                    display: flex; 
                    flex-direction: column; 
                    justify-content: center; 
                    align-items: center;
                    box-sizing: border-box;
                }

                .title { 
                    font-size: 8px; 
                    font-weight: bold; 
                    margin-bottom: 2px; 
                    white-space: nowrap; 
                    overflow: hidden; 
                    text-overflow: ellipsis; 
                    width: 100%;
                }

                .sn { 
                    font-size: 10px; 
                    font-weight: bold; 
                    color: #000; 
                    margin-bottom: 3px;
                }

                .qr-box svg { 
                    width: 70px; 
                    height: 70px; 
                }

                .footer { 
                    font-size: 7px; 
                    font-weight: bold; 
                    margin-top: 3px; 
                    border-top: 1px solid #ccc; 
                    padding-top: 2px;
                    width: 100%;
                }
            </style>
        </head>
        <body onload='window.print()'>
            <div class='title'>{$machine->tipe_model}</div>
            <div class='sn'>SN: {$machine->serial_number}</div>
            <div class='qr-box'>
                $qrCode
            </div>
            <div class='footer'>DGG - WORKSHOP HUB</div>
        </body>
        </html>";

        return response($html);
    }

    // dipindah dari: Route::get('/mesin/{id}/histori', ...)->name('mesin.histori')
    public function histori($id)
    {
        $machine = Machine::findOrFail($id);

        // 1. Ambil Riwayat Mutasi / Penempatan Mesin (Deployments)
        $deployments = DB::table('deployments')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')
            ->where('machine_id', $id)
            ->select('deployments.*', 'customers.nama_customer', 'customers.kota', 'technicians.nama_technician')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Ambil Riwayat Service/Perbaikan (Log Service)
        try {
            $serviceLogs = DB::table('service_logs')
                ->leftJoin('technicians', 'service_logs.technician_id', '=', 'technicians.id')
                ->where('machine_id', $id)
                ->select('service_logs.*', 'technicians.nama_technician')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $serviceLogs = collect([]); // Kosongkan jika belum bikin tabel service_logs
        }

        $html = "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Histori Mesin - {$machine->serial_number}</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-slate-100 text-slate-800 pb-10 font-sans'>
            
            <div class='bg-slate-900 text-white p-5 shadow-md sticky top-0 z-50'>
                <div class='max-w-md mx-auto'>
                    <span class='text-[10px] font-bold uppercase bg-blue-600 px-2 py-0.5 rounded text-white'>Daftar Histori Unit</span>
                    <h1 class='text-xl font-bold mt-1 text-yellow-400'>{$machine->tipe_model}</h1>
                    <p class='text-xs opacity-90 font-mono'>Serial Number: <b>{$machine->serial_number}</b></p>
                    <p class='text-xs mt-2'>Status Saat Ini: <span class='px-2 py-0.5 rounded bg-green-600 text-white font-bold text-[10px]'>{$machine->status}</span></p>
                </div>
            </div>

            <div class='max-w-md mx-auto px-4 mt-5 space-y-5'>
                
                <div>
                    <h2 class='text-xs font-bold text-slate-500 uppercase tracking-wider mb-2'>🔧 Jurnal Perbaikan & Log Service</h2>";
        if ($serviceLogs->isEmpty()) {
            $html .= "<div class='bg-white p-4 rounded-xl shadow-sm text-center text-slate-400 text-xs'>Belum ada catatan log perbaikan.</div>";
        } else {
            $html .= "<div class='space-y-3'>";
            foreach ($serviceLogs as $log) {
                $tgl = date('d-m-Y H:i', strtotime($log->created_at));
                $html .= "
                            <div class='bg-white p-4 rounded-xl shadow-sm border-l-4 border-amber-500'>
                                <div class='flex justify-between text-[10px] text-slate-400 font-bold'>
                                    <span>🛠️ Teknisi: {$log->nama_technician}</span>
                                    <span>📅 $tgl</span>
                                </div>
                                <div class='mt-2 text-xs'><b class='text-slate-700'>Kendala:</b> <span class='text-slate-600'>{$log->keluhan}</span></div>
                                <div class='mt-1 text-xs'><b class='text-slate-700'>Tindakan:</b> <span class='text-slate-600'>{$log->tindakan}</span></div>
                            </div>";
            }
            $html .= "</div>";
        }
        $html .= "</div>

                <div>
                    <h2 class='text-xs font-bold text-slate-500 uppercase tracking-wider mb-2'>📍 Riwayat Penempatan Pelanggan</h2>";
        if ($deployments->isEmpty()) {
            $html .= "<div class='bg-white p-4 rounded-xl shadow-sm text-center text-slate-400 text-xs'>Mesin ini belum pernah dikirim ke customer.</div>";
        } else {
            $html .= "<div class='space-y-3'>";
            foreach ($deployments as $dep) {
                $tglPasang = date('d-m-Y', strtotime($dep->tanggal_instal ?? $dep->created_at));
                $html .= "
                            <div class='bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-600'>
                                <div class='text-sm font-bold text-slate-800'>{$dep->nama_customer}</div>
                                <div class='text-xs text-slate-500'>📍 Lokasi: {$dep->kota}</div>
                                <div class='grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-100 text-[11px] text-slate-600'>
                                    <div><b>Tanggal Pasang:</b><br>$tglPasang</div>
                                    <div><b>Counter Awal:</b><br>BW: " . number_format($dep->counter_bw) . "<br>CL: " . number_format($dep->counter_color) . "</div>
                                </div>
                                <div class='text-[10px] text-slate-400 mt-2 border-t border-dashed border-slate-100 pt-1'>👷 Teknisi Pasang: {$dep->nama_technician}</div>
                            </div>";
            }
            $html .= "</div>";
        }
        $html .= "</div>

            </div>
        </body>
        </html>";

        return response($html);
    }

    // dipindah dari: Route::get('/cetak-stok-gudang', ...)->name('cetak.stok-gudang')
    public function stokGudang()
    {
        // Query Mesin Photo Copy
        $stocks = Machine::select(
            'tipe_model',
            'status',
            'asal_mesin',
            'volt',
            'kaset',
            'finisher',
            'double_scan',
            DB::raw('count(*) as total_unit'),
            DB::raw('SUM(IF(kaset = 4, 1, 0)) as kaset_4_count'),
            DB::raw('GROUP_CONCAT(serial_number ORDER BY serial_number SEPARATOR ", ") as list_sn')
        )
            ->whereIn('status', ['Ready', 'Refurbish'])
            ->groupBy('tipe_model', 'status', 'asal_mesin', 'volt', 'finisher', 'double_scan')
            ->orderBy('tipe_model', 'asc')
            ->orderByRaw("FIELD(status, 'Ready', 'Refurbish') asc")
            ->get();

        // Query Mesin Air RO (hanya yang status Ready & Perbaikan, belum di-deploy)
        $machineAirRos = \App\Models\MachineAirRo::select(
            'serial_number',
            'tipe_mesin',
            'status',
            DB::raw('count(*) as total_unit')
        )
            ->whereIn('status', ['Ready', 'Perbaikan'])
            ->whereNull('customer_ro_id') // Hanya yang belum di-deploy (customer_ro_id kosong)
            ->groupBy('serial_number', 'tipe_mesin', 'status')
            ->orderBy('tipe_mesin', 'asc')
            ->get();

        return view('print.stok-gudang', [
            'stocks'         => $stocks,
            'machineAirRos'  => $machineAirRos,
            'depo'           => 'Cirebon',
            'tanggal'        => now(),
            'dibuatOleh'     => null,
            'diketahuiOleh'  => null,
        ]);
    }


    public function stokGudangExcel()
    {
        return Excel::download(new StokGudangExport('Cirebon', now()), 'stock-mesin-' . now()->format('Ymd') . '.xlsx');
    }

    public function stokGudangPdf()
    {
        // Query Mesin Photo Copy
        $stocks = Machine::select(
            'tipe_model',
            'status',
            'asal_mesin',
            'volt',
            'kaset',
            'finisher',
            'double_scan',
            DB::raw('count(*) as total_unit'),
            DB::raw('SUM(IF(kaset = 4, 1, 0)) as kaset_4_count'),
            DB::raw('GROUP_CONCAT(serial_number ORDER BY serial_number SEPARATOR ", ") as list_sn')
        )
            ->whereIn('status', ['Ready', 'Refurbish'])
            ->groupBy('tipe_model', 'status', 'asal_mesin', 'volt', 'finisher', 'double_scan')
            ->orderBy('tipe_model', 'asc')
            ->orderByRaw("FIELD(status, 'Ready', 'Refurbish') asc")
            ->get();

        // Query Mesin Air RO
        $machineAirRos = \App\Models\MachineAirRo::select(
            'serial_number',
            'tipe_mesin',
            'status',
            DB::raw('count(*) as total_unit')
        )
            ->whereIn('status', ['Ready', 'Perbaikan'])
            ->whereNull('customer_ro_id')
            ->groupBy('serial_number', 'tipe_mesin', 'status')
            ->orderBy('tipe_mesin', 'asc')
            ->get();

        $pdf = Pdf::loadView('print.stok-gudang', [
            'stocks'         => $stocks,
            'machineAirRos'  => $machineAirRos,
            'depo'           => 'Cirebon',
            'tanggal'        => now(),
            'dibuatOleh'     => null,
            'diketahuiOleh'  => null,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 20)
            ->setOption('margin-right', 20);

        return $pdf->download('stock-mesin-' . now()->format('Ymd') . '.pdf');
    }
    // dipindah dari: Route::get('/cetak-alokasi-mesin', ...)->name('cetak.alokasi')
    // PERBAIKAN (24 Juni 2026) -- bagian 2:
    // Query ini pakai DB::table() (query builder polos), BUKAN Eloquent Model --
    // sehingga baris yang sudah di-soft-delete (kolom deleted_at terisi) di tabel
    // deployments dan customers TETAP ikut muncul di hasil, karena soft-delete
    // hanya otomatis terfilter kalau pakai Model::query() (Eloquent), bukan
    // DB::table(). Ditambahkan whereNull('deleted_at') untuk kedua tabel itu.
    public function alokasiMesin()
    {
        $data = DB::table('machines')
            ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
            ->where('machines.status', 'Rented')
            ->whereNull('deployments.deleted_at')
            ->whereNull('customers.deleted_at')
            ->select(
                'rayons.nama_rayon',
                'customers.kota',
                'machines.tipe_model',
                DB::raw('count(*) as qty')
            )
            ->groupBy('rayons.nama_rayon', 'customers.kota', 'machines.tipe_model')
            ->orderBy('rayons.nama_rayon')
            ->orderBy('customers.kota')
            ->get()
            ->groupBy(['nama_rayon', 'kota']);

        // Warna header atas untuk 4 rayon horizontal
        $rayonStyles = [
            0 => ['header_bg' => '#1e3a8a', 'text' => '#ffffff', 'kota_bg' => '#f0f9ff', 'kota_text' => '#0369a1'],
            1 => ['header_bg' => '#b45309', 'text' => '#ffffff', 'kota_bg' => '#fffbeb', 'kota_text' => '#92400e'],
            2 => ['header_bg' => '#047857', 'text' => '#ffffff', 'kota_bg' => '#f0fdf4', 'kota_text' => '#065f46'],
            3 => ['header_bg' => '#be185d', 'text' => '#ffffff', 'kota_bg' => '#fdf2f8', 'kota_text' => '#9d174d'],
        ];

        // --- STRATEGI PENYUSAUTAN BARIS (EQUAL HEIGHT BALANCING) ---
        $rowCountPerRayon = [];
        foreach ($data as $namaRayon => $kotas) {
            $lines = 0;
            foreach ($kotas as $namaKota => $types) {
                $lines++;
                $lines += count($types);
                $lines++;
            }
            $rowCountPerRayon[$namaRayon] = $lines;
        }

        $maxRows = count($rowCountPerRayon) > 0 ? max($rowCountPerRayon) : 0;

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Alokasi Mesin DGG</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }

                html, body {
                    width: 100%;
                    font-family: Arial, sans-serif;
                    color: #333;
                    background: #fff;
                }

                body {
                    padding: 15px 25px; /* Margin halaman kiri kanan atas bawah */
                }

                header {
                    text-align: center;
                    margin-bottom: 5px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 4px;
                }
                header h1 { font-size: 13px; color: #1e3a8a; font-weight: bold; }
                header h2 { font-size: 10px; margin: 2px 0; color: #475569; font-weight: bold; }
                header p  { font-size: 8px; color: #64748b; }

                /* Grid 4 Kolom Sejajar */
                .rayon-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 15px;               
                    margin-top: 15px;        
                    align-items: stretch; /* Memaksa semua box memiliki tinggi yang sama boksnya */
                }

                .rayon-block {
                    background: #fff;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between; /* Mendorong table tfoot terperangkap di paling bawah */
                    page-break-inside: avoid;
                }

                .rayon-header {
                    font-weight: bold;
                    font-size: 9px;
                    padding: 5px 6px;
                    text-transform: uppercase;
                    text-align: center;
                    letter-spacing: 0.5px;
                    border-radius: 2px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 4px;
                }

                th {
                    background: #0f172a;
                    color: #fff;
                    padding: 3px 5px;
                    font-size: 8px;
                    text-align: left;
                }
                th.text-right { text-align: right; }

                td { 
                    padding: 3px 5px; 
                    border-bottom: 1px solid #f1f5f9; 
                    font-size: 8px;
                    white-space: nowrap;
                }

                /* Desain baris Nama Kota */
                .bg-kota td {
                    font-size: 8px; 
                    padding: 3.5px 5px;
                    font-weight: bold;
                }

                /* Total Kota: Bold, Hitam Pekat, Garis bawah pembatas tegas */
                .bg-total-kota td {
                    color: #000000 !important;
                    font-weight: bold !important;
                    font-size: 8px;
                    border-bottom: 1.5px solid #000000; 
                    background: #fdfdfd;
                }

                /* Baris data kosong penyeimbang agar sejajar */
                .empty-row td {
                    border-bottom: 1px solid transparent; /* Hilangkan garis agar terlihat bersih kosongan */
                    color: transparent;
                }
                
                /* Total Akhir Rayon yang dikunci sejajar di bawah */
                .bg-total-rayon td {
                    font-weight: bold;
                    font-size: 8.5px;
                    color: #166534;
                    background-color: #e8f5e9;
                    border-top: 1.5px solid #166534;
                    padding: 5px 5px;
                }
                
                .text-right { text-align: right; }

                /* Baris Jingga Grand Total Paling Bawah */
                .grand-total {
                    margin-top: 15px;
                    background: #f97316;
                    color: #fff;
                    font-size: 10px;
                    font-weight: bold;
                    padding: 6px 12px;
                    text-align: right;
                    border-radius: 2px;
                    page-break-inside: avoid;
                   }

                    @mediaprint{
                    @page{
                        size: A4 landscape;
                        margin: 0;
                    }
                    body { 
                        padding: 8mm 12mm; 
                    }
                    .rayon-grid, .grand-total {
                        zoom: 94%; 
                    }
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
            </style>
        </head>
        <body onload='window.print()'>
            <header>
                <h1>PT DINAMIKA GLOBAL GEMILANG</h1>
                <h2>LAPORAN ALOKASI TYPE-TYPE MESIN PER RAYON</h2>
                <p>Tanggal Cetak: " . date('d-m-Y H:i') . "</p>
            </header>

            <div class='rayon-grid'>";

        $grandTotal = 0;
        $rayonIndex = 0;

        foreach ($data as $namaRayon => $kotas) {
            $totalRayon = 0;
            $currentStyle = $rayonStyles[$rayonIndex % 4];
            $currentRows = $rowCountPerRayon[$namaRayon];

            $html .= "
            <div class='rayon-block'>
                <div>
                    <div class='rayon-header' style='background-color: {$currentStyle['header_bg']}; color: {$currentStyle['text']};'>
                        RAYON -  " . strtoupper($namaRayon) . "
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>KOTA / TIPE MESIN</th>
                                <th class='text-right' width='40'>UNIT</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($kotas as $namaKota => $types) {
                $totalKota = 0;
                $html .= "
                <tr class='bg-kota' style='background-color: {$currentStyle['kota_bg']}; color: {$currentStyle['kota_text']};'>
                    <td colspan='2'> $namaKota</td>
                </tr>";

                foreach ($types as $item) {
                    $html .= "<tr>
                        <td>&nbsp;&nbsp;• {$item->tipe_model}</td>
                        <td class='text-right'>{$item->qty}</td>
                      </tr>";
                    $totalKota += $item->qty;
                }

                $html .= "
                <tr class='bg-total-kota'>
                    <td class='text-right'>Total $namaKota:</td>
                    <td class='text-right'>$totalKota</td>
                </tr>";

                $totalRayon += $totalKota;
            }

            if ($currentRows < $maxRows) {
                $neededPadding = $maxRows - $currentRows;
                for ($i = 0; $i < $neededPadding; $i++) {
                    $html .= "<tr class='empty-row'><td>&nbsp;</td><td>&nbsp;</td></tr>";
                }
            }

            $html .= "
                        </tbody>
                    </table>
                </div>
                
                <!-- Bagian ini otomatis terkunci sejajar lurus horizontal di bawah -->
                <table>
                    <tfoot>
                        <tr class='bg-total-rayon'>
                            <td class='text-right'>TOTAL " . strtoupper($namaRayon) . " :</td>
                            <td class='text-right' width='40'>$totalRayon Pcs</td>
                        </tr>
                    </tfoot>
                </table>
            </div>";

            $grandTotal += $totalRayon;
            $rayonIndex++;
        }

        $html .= "
            </div>
            <div class='grand-total'>GRAND TOTAL UNIT TERPASANG: $grandTotal UNIT</div>
        </body>
        </html>";

        return response($html);
    }

    // dipindah dari: Route::get('/cetak-surat-jalan/{id}', ...)->name('cetak.surat-jalan')
    public function suratJalan($id)
    {
        $d = Deployment::with(['machine', 'customer'])->findOrFail($id);

        $html = "<!DOCTYPE html>
        <html>
        <head>
            <title>SJ - {$d->machine->serial_number}</title>
            <style>
    @page{
                    size: A5 landscape; 
                    margin: 0mm;
                }
                html, body {
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .wrapper-sj { 
                    font-family: sans-serif; 
                    font-size: 11px; 
                    border: 2px solid #000; 
                    padding: 20px;
                    height: 100vh; 
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between; 
                }
                .content-top {
                    width: 100%;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin: 15px 0; 
                } 
                th, td { 
                    border: 1px solid #000; 
                    padding: 8px; 
                } 
                th { 
                    background: #f2f2f2; 
                }
                .signature-section {
                    display: flex; 
                    justify-content: space-around; 
                    margin-top: auto; 
                    text-align: center;
                    padding-bottom: 10px;
                }
            </style>
        </head>
        <body onload='window.print()'>
            <div class='wrapper-sj'>
                <div class='content-top'>
                    <div style='display:flex; justify-content:space-between; border-bottom:2px solid #000; padding-bottom:5px;'>
                        <div>
                            <h2 style='margin:0 0 5px 0; font-size:16px;'>PT. DINAMIKA GLOBAL GEMILANG</h2>
                            <p style='margin:0; font-weight:bold; letter-spacing:1px;'>PEMASANGAN MESIN BARU</p>
                        </div>
                        <div style='text-align:right;'>
                            <p style='margin:0 0 5px 0;'><b>Nomor: SJ/FC/CRB/" . date('dmy', strtotime($d->created_at)) . "/" . str_pad($d->id, 3, '0', STR_PAD_LEFT) . "</b></p>
                            <p style='margin:0;'>Tanggal: " . date('d-m-Y', strtotime($d->created_at)) . "</p>
                        </div>
                    </div>
                    
                    <p style='margin-top:15px; margin-bottom:15px; line-height: 1.4;'>
                        <b>Penerima:</b> {$d->customer->nama_customer}<br>
                        <b>Alamat :</b> {$d->customer->alamat}
                    </p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width='30'>NO</th>
                                <th>NAMA BARANG / DESKRIPSI</th>
                                <th>SN / KODE PART</th>
                                <th width='70'>JUMLAH</th>
                                <th>KETERANGAN / CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style='text-align:center;'>1</td>
                                <td><b>Mesin Fotokopi {$d->machine->tipe_model}</b></td>
                                <td>{$d->machine->serial_number}</td>
                                <td style='text-align:center;'>1 Unit</td>
                                <td></td>
                            </tr>";

        // 1. Ambil data Sparepart
        $no = 2;
        $parts = DB::table('deployment_sparepart')
            ->join('spareparts', 'deployment_sparepart.sparepart_id', '=', 'spareparts.id')
            ->where('deployment_sparepart.deployment_id', $id)
            ->select('spareparts.nama_sparepart', 'spareparts.code_part', 'deployment_sparepart.jumlah')
            ->get();

        foreach ($parts as $p) {
            $html .= "<tr>
                                    <td style='text-align:center;'>$no</td>
                                    <td><b>" . strtoupper($p->nama_sparepart) . "</b></td>
                                    <td>{$p->code_part}</td>
                                    <td style='text-align:center;'>{$p->jumlah} Pcs</td>
                                    <td></td>
                                </tr>";
            $no++;
        }

        // 🌟 REVISI BARIS KOTAK KOSONG: 
        // Jika total item kurang dari 8 baris, tambahkan baris kotak kosong sampai pas menjadi 8 baris.
        $targetBaris = 8;
        $totalItemSekarang = $no - 1; // Jumlah data terisi (Mesin + Sparepart)

        if ($totalItemSekarang < $targetBaris) {
            for ($i = ($totalItemSekarang + 1); $i <= $targetBaris; $i++) {
                $html .= "<tr>
                                        <td style='text-align:center; color: transparent;'>$i</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>";
            }
        }

        $html .= "      </tbody>
                    </table>
                </div>

                <div class='signature-section'>
                    <div>Admin,<br><br><br><br>( ________________ )</div>
                    <div>Disetujui,<br><br><br><br>( ________________ )</div>
                    <div>Teknisi,<br><br><br><br>( ________________ )</div>
                    <div>Penerima,<br><br><br><br>( ________________ )</div>
                </div>
            </div>
        </body>
        </html>";

        return response($html);
    }

    public function trackingMesin($id)
    {
        $machine = \App\Models\Machine::with(['customer'])->findOrFail($id);

        // Kumpulkan semua event dalam timeline
        $timeline = collect();

        // 1. Semua deployment (aktif maupun sudah ditarik)
        $deployments = DB::table('deployments')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')
            ->where('deployments.machine_id', $id)
            ->select(
                'deployments.id',
                'deployments.tanggal_instal',
                'deployments.tanggal_tarik',
                'deployments.deleted_at',
                'deployments.counter_bw',
                'deployments.counter_color',
                'deployments.no_kontrak',
                'customers.nama_customer',
                'customers.kota',
                'customers.alamat',
                'technicians.nama_technician',
            )
            ->orderBy('deployments.tanggal_instal')
            ->get();

        foreach ($deployments as $dep) {
            // Event: Dipasang ke customer
            $timeline->push([
                'tanggal'   => $dep->tanggal_instal,
                'tipe'      => 'RENTAL',
                'icon'      => '📦',
                'warna'     => 'green',
                'judul'     => 'Dipasang ke Customer',
                'detail'    => $dep->nama_customer . ' — ' . $dep->kota,
                'sub'       => 'Teknisi: ' . ($dep->nama_technician ?? '-') . ' | Counter Awal BW: ' . number_format($dep->counter_bw) . ' / CL: ' . number_format($dep->counter_color) . ($dep->no_kontrak ? ' | Kontrak: ' . $dep->no_kontrak : ''),
            ]);

            // Event: Ditarik dari customer
            if ($dep->tanggal_tarik || $dep->deleted_at) {
                $tglTarik = $dep->tanggal_tarik ?? \Carbon\Carbon::parse($dep->deleted_at)->toDateString();
                $timeline->push([
                    'tanggal'   => $tglTarik,
                    'tipe'      => 'TARIK',
                    'icon'      => '🔙',
                    'warna'     => 'orange',
                    'judul'     => 'Ditarik dari Customer',
                    'detail'    => $dep->nama_customer . ' — ' . $dep->kota,
                    'sub'       => '',
                ]);
            }
        }

        // 2. Rolling — mesin ini pernah jadi mesin LAMA (diambil dari customer)
        $rollingLama = DB::table('machine_replacements')
            ->join('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->join('machines as m_new', 'machine_replacements.new_machine_id', '=', 'm_new.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->where('machine_replacements.old_machine_id', $id)
            ->select(
                'machine_replacements.tanggal',
                'machine_replacements.keterangan',
                'machine_replacements.counter_bw_final',
                'machine_replacements.counter_color_final',
                'customers.nama_customer',
                'customers.kota',
                'm_new.serial_number as sn_baru',
                'technicians.nama_technician',
            )
            ->get();

        foreach ($rollingLama as $r) {
            $timeline->push([
                'tanggal'   => $r->tanggal,
                'tipe'      => 'ROLLING_KELUAR',
                'icon'      => '🔄',
                'warna'     => 'red',
                'judul'     => 'Rolling — Mesin Diganti (Keluar)',
                'detail'    => 'Diganti dari ' . $r->nama_customer . ' oleh SN Baru: ' . $r->sn_baru,
                'sub'       => 'Counter Akhir BW: ' . number_format($r->counter_bw_final) . ' / CL: ' . number_format($r->counter_color_final) . ' | Teknisi: ' . ($r->nama_technician ?? '-') . ($r->keterangan ? ' | ' . $r->keterangan : ''),
            ]);
        }

        // 3. Rolling — mesin ini pernah jadi mesin BARU (pengganti)
        $rollingBaru = DB::table('machine_replacements')
            ->join('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->join('machines as m_old', 'machine_replacements.old_machine_id', '=', 'm_old.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->where('machine_replacements.new_machine_id', $id)
            ->select(
                'machine_replacements.tanggal',
                'machine_replacements.keterangan',
                'customers.nama_customer',
                'customers.kota',
                'm_old.serial_number as sn_lama',
                'technicians.nama_technician',
            )
            ->get();

        foreach ($rollingBaru as $r) {
            $timeline->push([
                'tanggal'   => $r->tanggal,
                'tipe'      => 'ROLLING_MASUK',
                'icon'      => '✅',
                'warna'     => 'blue',
                'judul'     => 'Rolling — Masuk sebagai Pengganti',
                'detail'    => 'Menggantikan SN: ' . $r->sn_lama . ' di ' . $r->nama_customer,
                'sub'       => 'Teknisi: ' . ($r->nama_technician ?? '-') . ($r->keterangan ? ' | ' . $r->keterangan : ''),
            ]);
        }

        // Urutkan timeline berdasarkan tanggal
        $timeline = $timeline->sortBy('tanggal')->values();

        return view('print.tracking-mesin', compact('machine', 'timeline'));
    }
}
