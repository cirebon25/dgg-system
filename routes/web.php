<?php

use App\Models\Deployment;
use App\Models\Machine;
use App\Models\Rayon;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use App\Models\Sparepart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Untuk nampilin halaman login
// Route::get('/admin/login', [LoginController::class, 'showLoginForm']);

// Untuk proses kirim data login (Ini yang error tadi)
// Route::post('/admin/login', [LoginController::class, 'login']);

Route::get('/print-service-bulk', function (Request $request) {
    // Ambil ID yang dikirim dari tombol centang di Filament
    $ids = explode(',', $request->ids);
    $records = ServiceLog::with(['machine', 'technician', 'sparepart'])
        ->whereIn('id', $ids)
        ->orderBy('tanggal', 'asc')
        ->get();

    return view('print-service-bulk', compact('records'));
})->name('print.service.bulk')->middleware('auth');

// Jalur khusus untuk cetak service log
Route::get('/service-log/{record}/print', function (ServiceLog $record) {
    return view('print.service-log', ['record' => $record]);
})->name('service-log.print');

// ROUTE CETAK BULANAN
Route::get('/service-log/report/monthly', function (Request $request) {
    $month = $request->query('month');
    $year = $request->query('year');

    // TARIK DATA LENGKAP TERMASUK DEPLOYMENT DAN SPAREPART
    $logs = ServiceLog::whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->with(['machine.deployment.customer', 'technician', 'serviceLogSpareparts.sparepart'])
        ->orderBy('tanggal', 'asc') // Urutkan dari tanggal terawal
        ->get();

    return view('print.monthly-report', [
        'logs' => $logs,
        'month' => $month,
        'year' => $year,
    ]);
})->name('service-log.monthly');


Route::get('/sparepart/monitor-umur/{machine_id}', function ($machine_id) {
    $machine = Machine::with(['deployment.customer', 'serviceLogs'])->findOrFail($machine_id);

    // Ambil semua histori pergantian sparepart di mesin ini
    $partHistories = ServiceLogSparepart::whereHas('serviceLog', function ($q) use ($machine_id) {
        $q->where('machine_id', $machine_id);
    })->with(['sparepart', 'serviceLog'])->get();

    // Counter sekarang adalah counter terakhir dari ServiceLog terbaru
    $latestLog = $machine->serviceLogs()->latest('tanggal')->first();
    $counterSekarangBW = $latestLog->counter_bw ?? 0;
    $counterSekarangCL = $latestLog->counter_color ?? 0;

    return view('print.sparepart-monitoring', compact('machine', 'partHistories', 'counterSekarangBW', 'counterSekarangCL'));
})->name('sparepart.monitor');

Route::get('/cetak-alokasi-mesin', function () {
    // Ambil data (Logika asli tetap dipertahankan, tidak diganti)
    $data = DB::table('machines')
        ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
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

    // Tampilan HTML-nya (Dimodifikasi CSS & Struktur baris jeda)
    $html = "
    <html>
    <head>
        <title>Laporan Alokasi Mesin DGG</title>
        <style>
            body { font-family: sans-serif; font-size: 12px; padding: 20px; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            
            .bg-rayon { background-color: #1e3a8a !important; color: white !important; font-weight: bold; font-size: 13px; }
            .bg-kota { background-color: #e0f2fe !important; color: #0369a1 !important; font-weight: bold; }
            .bg-total-kota { background-color: #fffde7; font-style: italic; }
            .bg-total-rayon { background-color: #dcfce7; font-weight: bold; font-size: 13px; color: #15803d; }
            
            /* -- KELAS KHUSUS UNTUK MEMBERI SPACE JEDA ANTAR RAYON -- */
            .spacer-row td { border: none !important; background: white !important; height: 25px; }
            
            .text-right { text-align: right; }
            header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            
            @media print {
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }
        </style>
    </head>
    <body onload='window.print()'>
        <header>
            <h1 style='margin:0; color: #1e3a8a;'>PT DINAMIKA GLOBAL GEMILANG</h1>
            <h2 style='margin:5px 0;'>LAPORAN TYPE-TYPE MESIN</h2>
            <p>Dicetak pada: " . date('d-m-Y H:i') . "</p>
        </header>
        <table>
            <thead>
                <tr style='background: #0f172a; color: #fff;'>
                    <th>RAYON / KOTA / TIPE MESIN</th>
                    <th width='150' class='text-right'>JUMLAH UNIT</th>
                </tr>
            </thead>
            <tbody>";

    $grandTotal = 0;
    foreach ($data as $namaRayon => $kotas) {
        // Baris Rayon sekarang otomatis berwarna Biru Gelap yang tegas dengan teks putih
        $html .= "<tr class='bg-rayon'><td colspan='2'>🔹 RAYON: " . strtoupper($namaRayon) . "</td></tr>";
        $totalRayon = 0;
        foreach ($kotas as $namaKota => $types) {
            // Baris Kota menggunakan warna Biru Muda yang soft agar kontrasnya enak dilihat
            $html .= "<tr class='bg-kota'><td colspan='2'>&nbsp;&nbsp;📍 Kota/Kab: $namaKota</td></tr>";
            $totalKota = 0;
            foreach ($types as $item) {
                $html .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; • {$item->tipe_model}</td><td class='text-right'>{$item->qty} Unit</td></tr>";
                $totalKota += $item->qty;
            }
            $html .= "<tr class='bg-total-kota'><td class='text-right'>Total Unit di $namaKota:</td><td class='text-right'>$totalKota Unit</td></tr>";
            $totalRayon += $totalKota;
        }
        $html .= "<tr class='bg-total-rayon'><td class='text-right'>TOTAL AKUMULASI RAYON " . strtoupper($namaRayon) . ":</td><td class='text-right'>$totalRayon Unit</td></tr>";

        // ✨ MODIFIKASI UTAMA: Menyisipkan baris kosong hantu tanpa border sebagai space jeda antar Rayon
        $html .= "<tr class='spacer-row'><td colspan='2'></td></tr>";

        $grandTotal += $totalRayon;
    }

    $html .= "
            </tbody>
            <tfoot>
                <tr style='background: #FF8C00; color: #000000; font-size: 15px; font-weight: bold;'>
                    <td class='text-right'>GRAND TOTAL UNIT TERPASANG:</td>
                    <td class='text-right'>$grandTotal Unit</td>
                </tr>
            </tfoot>
        </table>
    </body>
    </html>";

    return response($html);
})->name('cetak.alokasi');


// =========================================================================
// 3. ROUTE REKAP TUKAR GULING (SWAP) - REVISI TOTAL STRUKTUR MATRIKS KOLOM
// =========================================================================
Route::get('/cetak-tukar-guling', function () {
    $data = DB::table('machine_replacements')
        ->leftJoin('customers', 'machine_replacements.customer_id', '=', 'customers.id')
        ->leftJoin('machines as m_old', 'machine_replacements.old_machine_id', '=', 'm_old.id')
        ->leftJoin('machines as m_new', 'machine_replacements.new_machine_id', '=', 'm_new.id')
        ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
        ->leftJoin('service_logs as log_old', function ($join) {
            $join->on('machine_replacements.old_machine_id', '=', 'log_old.machine_id')
                ->on('machine_replacements.customer_id', '=', 'log_old.customer_id')
                ->on('machine_replacements.tanggal', '=', 'log_old.tanggal')
                ->where('log_old.kerusakan', '=', 'ROLLING OUT');
        })
        ->leftJoin('service_logs as log_new', function ($join) {
            $join->on('machine_replacements.new_machine_id', '=', 'log_new.machine_id')
                ->on('machine_replacements.customer_id', '=', 'log_new.customer_id')
                ->on('machine_replacements.tanggal', '=', 'log_new.tanggal')
                ->where('log_new.kerusakan', '=', 'ROLLING IN');
        })
        ->leftJoin('deployments', 'machine_replacements.customer_id', '=', 'deployments.customer_id')
        ->select(
            'machine_replacements.tanggal',
            'customers.nama_customer',
            'customers.kota',
            'm_old.serial_number as sn_lama',
            'm_old.tipe_model as tipe_lama',
            'm_new.serial_number as sn_baru',
            'm_new.tipe_model as tipe_baru',
            'technicians.nama_technician',
            'log_old.counter_bw as counter_bw_old',
            'log_old.counter_color as counter_color_old',
            'log_old.perbaikan as alasan_ganti',
            'log_new.counter_bw as counter_bw_new',
            'log_new.counter_color as counter_color_new',
            'deployments.volt as volt_mesin'
        )
        ->orderBy('machine_replacements.tanggal', 'desc')
        ->get();

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Laporan Tukar Guling DGG</title>
        <style>
            @page { size: landscape; margin: 8mm; }
            body { font-family: sans-serif; font-size: 10px; padding: 10px; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #000; padding: 6px 4px; vertical-align: middle; }
            
            /* TH UTAMA */
            th { 
                text-align: center; 
                color: #000000 !important; 
                font-weight: bold; 
                text-transform: uppercase;
                font-size: 9px;
            }
            
            /* 🌟 STYLING WARNA HEADER REQUEST USER */
            .th-normal { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .th-awal { background-color: #2563eb !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .th-awal-sub { background-color: #93c5fd !important; color: #000000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .th-new { background-color: #16a34a !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .th-new-sub { background-color: #86efac !important; color: #000000 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            
            /* 🌟 STYLING BG KOLOM REQUEST USER */
            .td-awal { background-color: #eff6ff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .td-new { background-color: #f0fdf4 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            
            .header-box { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
            .text-center { text-align: center; }
            .font-bold { font-weight: bold; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header-box'>
            <h1 style='margin:0; color:#000; font-size: 18px;'>DGG SYSTEM - OPERATIONAL HUB</h1>
            <h2 style='margin:4px 0; color:#000; font-size: 13px;'>BERITA ACARA & REKAP LAPORAN TUKAR GULING MESIN (SWAP)</h2>
            <p style='font-weight:bold; margin:0;'>Periode: " . date('F Y') . "</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan='2' width='3%' class='th-normal'>No</th>
                    <th rowspan='2' width='7%' class='th-normal'>Tgl</th>
                    <th rowspan='2' width='14%' class='th-normal'>Nama Customer</th>
                    <th colspan='4' class='th-awal'>UNIT AWAL (DITARIK)</th>
                    <th colspan='4' class='th-new'>UNIT BARU (TERPASANG)</th>
                    <th rowspan='2' width='12%' class='th-normal'>Keterangan Ganti</th>
                </tr>
                <tr>
                    <th width='9%' class='th-awal-sub'>Tipe Awal</th>
                    <th width='9%' class='th-awal-sub'>NS</th>
                    <th width='10%' class='th-awal-sub'>Conter Mesin Awal</th>
                    <th width='6%' class='th-awal-sub'>Volt Mesin Awal</th>
                    <th width='9%' class='th-new-sub'>Tipe Mesin New</th>
                    <th width='9%' class='th-new-sub'>NS</th>
                    <th width='10%' class='th-new-sub'>Conter</th>
                    <th width='6%' class='th-new-sub'>Volt</th>
                </tr>
            </thead>
            <tbody>";

    if ($data->isEmpty()) {
        $html .= "<tr><td colspan='12' class='text-center' style='padding:20px; font-weight:bold; color:#666;'>Belum ada riwayat transaksi tukar guling (swap) mesin.</td></tr>";
    } else {
        foreach ($data as $index => $row) {
            $no = $index + 1;
            $tgl = $row->tanggal ? date('d/m/Y', strtotime($row->tanggal)) : date('d/m/Y');
            $customer = "<strong>" . strtoupper($row->nama_customer ?? 'Umum') . "</strong><br><small style='color:#555;'>$row->kota</small>";
            $voltase = !empty($row->volt_mesin) ? trim($row->volt_mesin) . ' V' : '220 V';

            $html .= "
            <tr>
                <td class='text-center'>$no</td>
                <td class='text-center'>$tgl</td>
                <td>$customer</td>
                
                <td class='td-awal'> " . ($row->tipe_lama ?? '-') . " </td>
                <td class='td-awal font-bold'> " . ($row->sn_lama ?? '-') . " </td>
                <td class='td-awal'>
                    BW: " . number_format($row->counter_bw_old ?? 0) . "<br>
                    CL: " . number_format($row->counter_color_old ?? 0) . "
                </td>
                <td class='td-awal text-center font-bold' style='color:#1e40af;'> $voltase </td>
                
                <td class='td-new'> " . ($row->tipe_baru ?? '-') . " </td>
                <td class='td-new font-bold'> " . ($row->sn_baru ?? '-') . " </td>
                <td class='td-new'>
                    BW: " . number_format($row->counter_bw_new ?? 0) . "<br>
                    CL: " . number_format($row->counter_color_new ?? 0) . "
                </td>
                <td class='td-new text-center font-bold' style='color:#16a34a;'> $voltase </td>
                
                <td> " . ($row->alasan_ganti ?? 'Rolling Unit') . " </td>
            </tr>";
        }
    }

    $html .= "
            </tbody>
        </table>
        <div style='margin-top: 35px; float: right; width: 220px; text-align: center;'>
            <p>Indramayu, " . date('d M Y') . "</p>
            <br><br><br>
            <strong>( ________________ )</strong><br>
            <p style='margin:5px 0; font-weight:bold;'>Admin Operasional</p>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.swap');

// FITUR 2: LAPORAN ALOKASI CUSTOMER (Mesin yang sedang terpasang)
Route::get('/cetak-alokasi-customer', function () {
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
})->name('cetak.alokasi-customer');

// ➡️ RUTE 1: UNTUK MENCETAK STIKER QR CODE (Ukuran Presisi Stiker)
Route::get('/mesin/{id}/cetak-qr', function ($id) {
    try {
        $machine = Machine::findOrFail($id);
    } catch (\Exception $e) {
        return 'Data mesin tidak ditemukan!';
    }

    // URL yang akan dibuka saat scan (Histori Mesin)
    $urlHistori = route('mesin.histori', ['id' => $machine->id]);

    // Membuat gambar QR Code (Pakai format SVG agar tajam saat di-print)
    $qrCode = QrCode::size(100)->margin(0)->generate($urlHistori);

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>QR Mesin - {$machine->serial_number}</title>
        <style>
            /* 🛠️ SETTING UKURAN STIKER THERMAL (50mm x 40mm) */
            @page {
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
})->name('mesin.cetak-qr');

// ➡️ RUTE 2: HALAMAN YANG TERBUKA DI HP SAAT QR CODE DI-SCAN
Route::get('/mesin/{id}/histori', function ($id) {
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
})->name('mesin.histori');

Route::get('/cetak-rekap-rayon', function (Request $request) {
    $month = $request->query('month', date('m'));
    $year = $request->query('year', date('Y'));

    // Ambil semua rayon dan ikut sertakan customer -> deployment -> mesin
    $rayons = Rayon::with(['customers.deployments.machine'])->get();

    $namaBulan = Carbon::createFromFormat('m', $month)->translatedFormat('F');

    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Helvetica', sans-serif; font-size: 11px; padding: 10px; }
            header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
            .rayon-box { margin-bottom: 30px; }
            .rayon-title { background: #333; color: #fff; padding: 8px; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; }
            th { background: #f2f2f2; border: 1px solid #000; padding: 8px; text-transform: uppercase; }
            td { border: 1px solid #000; padding: 8px; vertical-align: top; }
            .text-center { text-align: center; }
            .empty { color: #999; font-style: italic; }
        </style>
    </head>
    <body onload='window.print()'>
        <header>
            <h1 style='margin:0;'>DGG SYSTEM - MONITORING UNIT</h1>
            <h2 style='margin:5px 0;'>LAPORAN KINERJA MESIN PER RAYON</h2>
            <p>Periode: $namaBulan $year</p>
        </header>";

    foreach ($rayons as $rayon) {
        $html .= "<div class='rayon-box'>
                    <div class='rayon-title'>📍 RAYON: " . strtoupper($rayon->nama_rayon) . "</div>
                    <table>
                        <thead>
                            <tr>
                                <th width='30'>NO</th>
                                <th width='200'>NAMA CUSTOMER & KOTA</th>
                                <th width='150'>SN / MODEL</th>
                                <th>HISTORI SERVICE ($namaBulan)</th>
                            </tr>
                        </thead>
                        <tbody>";

        $i = 1;
        foreach ($rayon->customers as $customer) {
            foreach ($customer->deployments as $dep) {
                $machine = $dep->machine;
                if (! $machine) {
                    continue;
                }

                // Ambil log service untuk mesin ini di bulan terpilih
                $logs = ServiceLog::where('machine_id', $machine->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->get();

                $html .= "<tr>
                            <td class='text-center'>$i</td>
                            <td><b>{$customer->nama_customer}</b><br>{$customer->kota}</td>
                            <td><b>{$machine->serial_number}</b><br>{$machine->tipe_model}</td>
                            <td>";

                if ($logs->isEmpty()) {
                    $html .= "<span class='empty'>- Unit Normal (Tidak Ada Tindakan) -</span>";
                } else {
                    $html .= "<ul style='margin:0; padding-left:15px;'>";
                    foreach ($logs as $log) {
                        $tgl = date('d/m', strtotime($log->created_at));
                        $html .= "<li><b>[$tgl]</b> {$log->keluhan} -> <i>{$log->tindakan}</i></li>";
                    }
                    $html .= "</ul>";
                }

                $html .= "</td></tr>";
                $i++;
            }
        }

        if ($i == 1) {
            $html .= "<tr><td colspan='4' class='text-center empty'>Tidak ada mesin terpasang di wilayah ini.</td></tr>";
        }

        $html .= "</tbody></table></div>";
    }

    $html .= "</body></html>";

    return response($html);
})->name('cetak.rekap-rayon');

Route::get('/cetak-service-rayon', function (Request $request) {
    $month = $request->query('month', date('m'));
    $year = $request->query('year', date('Y'));

    // Ambil Rayon yang punya data Customer & Deployment
    $rayons = Rayon::with(['customers.deployments.machine'])->get();
    $namaBulan = Carbon::createFromFormat('m', $month)->translatedFormat('F');

    return view('filament.pages.cetak-rekap-rayon', [
        'rayons' => $rayons,
        'month' => $month,
        'year' => $year,
        'namaBulan' => $namaBulan,
    ]);
})->name('cetak.service-rayon');

Route::get('/cetak-top-usage', function (Request $request) {
    $month = $request->month;
    $year = $request->year;

    $records = ServiceLog::query()
        ->join('machines', 'service_logs.machine_id', '=', 'machines.id')
        ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->select(
            'customers.nama_customer',
            'machines.serial_number',
            DB::raw('SUM(usage_bw) as total_bw'),
            DB::raw('SUM(usage_color) as total_color'),
            DB::raw('SUM(usage_bw + usage_color) as total_semua')
        )
        ->whereMonth('service_logs.tanggal', $month)
        ->whereYear('service_logs.tanggal', $year)
        ->groupBy('customers.nama_customer', 'machines.serial_number')
        ->orderBy('total_semua', 'desc')->get();

    return view('filament.pages.cetak-top-usage', [
        'records' => $records,
        'bulan' => Carbon::create()->month($month)->translatedFormat('F'),
        'tahun' => $year,
    ]);
})->name('cetak.top-usage');

Route::get('/cetak-sj-rolling', function () {
    $data = session('sj_data');
    if (! $data) {
        return 'Data tidak ditemukan, silakan input ulang.';
    }

    return view('cetak.surat-jalan-rolling', [
        'd' => $data,
        'tanggal' => date('d/m/Y'),
        'nomor_sj' => 'SJ-RR/' . date('Ymd/Hi'),
    ]);
})->name('cetak.sj-rolling');

Route::get('/cetak-stok-gudang', function () {
    // 1. KUNCINYA: Grouping HANYA berdasarkan tipe_model dan volt
    $stocks = Machine::where('status', 'Ready')
        ->select(
            'tipe_model',
            'volt',
            'kaset',
            'finisher',
            'double_scan',
            DB::raw('count(*) as total_unit'),
            // Kita kumpulkan semua info kaset, finisher, dsb ke dalam kolom keterangan saja
            DB::raw('GROUP_CONCAT(serial_number SEPARATOR ", ") as list_sn'),
            DB::raw('GROUP_CONCAT(CONCAT(serial_number, "(K:", kaset, "/F:", finisher, ")") SEPARATOR " | ") as detail_unit')
        )
        ->groupBy('tipe_model', 'volt')
        ->orderBy('tipe_model', 'asc')
        ->get();

    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <style>
            @page { size: A4 landscape; margin: 10mm; }
            body { font-family: Arial, sans-serif; font-size: 12px; }
            header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 12px 8px; text-align: center; }
            th { background: #f2f2f2; text-transform: uppercase; font-size: 11px; }
            .bg-ready { background: #e0f2fe; font-weight: bold; font-size: 16px; }
            .text-left { text-align: left; font-size: 10px; color: #333; }
        </style>
    </head>
    <body onload='window.print()'>
        <header>
            <h1 style='margin:0;'>DINAMIKA GLOBAL GEMILANG (DGG)</h1>
            <h2 style='margin:5px 0;'>REKAPITULASI STOK UNIT GUDANG</h2>
            <p>Posisi Stok: " . date('d-m-Y H:i') . "</p>
        </header>

        <table>
            <thead>
               <tr>
                    <th width='30'>NO</th>
                    <th width='180'>TIPE MESIN</th>
                    <th width='60'>VOLT</th>
                    <th width='80' class='bg-blue'>TOTAL UNIT</th>
                    <th width='60'>KASET</th>
                    <th width='60'>FINISHER</th>
                    <th width='60'>D. SCAN</th>
                    <th class='text-left'>LIST SN / KETERANGAN</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($stocks as $index => $s) {
        $no = $index + 1;
        $html .= "
            <tr>
                <td>$no</td>
                <td class='text-left font-bold'>{$s->tipe_model}</td>
                <td><span class='font-bold'>" . ($s->volt ?: '-') . "V</span></td>
                <td class='bg-blue'>{$s->total_unit} UNIT</td>
                <td>" . ($s->kaset ?: 0) . "</td>
                <td>" . ($s->finisher ?: 0) . "</td>
                <td>" . ($s->double_scan ?: 0) . "</td>
                <td class='text-left' style='font-size:9px;'>
                    <strong>SN:</strong> {$s->list_sn}<br>
                    <small>Ket: " . ($s->info ?: '-') . "</small>
                </td>
            </tr>";
    }

    $html .= "
            </tbody>
        </table>
        <h3 style='text-align:right;'>GRAND TOTAL STOK READY: " . $stocks->sum('total_unit') . " UNIT</h3>
    </body>
    </html>";

    return response($html);
})->name('cetak.stok-gudang');

Route::get('/admin/service-log/{serviceLog}/surat-jalan', function (\App\Models\ServiceLog $serviceLog) {
    // Kita panggil view khusus surat jalan yang sudah kita buat kemarin
    return view('reports.surat-jalan', ['log' => $serviceLog]);
})->name('service-log.surat-jalan')->middleware(['auth']);

Route::get('/admin/rekap-horizontal', [App\Http\Controllers\ReportController::class, 'rekapHorizontal'])
    ->name('rekap.horizontal')->middleware(['auth']);


// =========================================================================
// 1. ROUTE LAPORAN PEMASANGAN BARU (FONT HEADER HITAM + FIX FILTER PILIHAN)
// =========================================================================
Route::get('/cetak-pemasangan-baru/{bulan?}/{tahun?}', function ($bulan = null, $tahun = null) {
    // 🌟 KUNCI FIX FILTER: Jika ada kiriman dari form Filament gunakan itu, jika kosong baru pakai bulan berjalan
    $bulan = $bulan ?? date('m');
    $tahun = $tahun ?? date('Y');

    $data = Deployment::with(['machine', 'customer.rayon', 'technician'])
        ->whereMonth('created_at', $bulan)
        ->whereYear('created_at', $tahun)
        ->orderBy('created_at', 'asc')
        ->get();

    $bulanIndo = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    $namaBulan = $bulanIndo[$bulan] ?? 'Tidak Diketahui';

    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Laporan Pemasangan Baru</title>
        <style>
            @page { size: landscape; margin: 10mm; }
            body { font-family: sans-serif; font-size: 11px; color: #333; } 
            table { width: 100%; border-collapse: collapse; margin-top: 10px; } 
            
            /* 🌟 REVISI UTAMA: Font Header No s/d Keterangan Wajib Hitam Pekat */
            th { background-color: #f2f2f2; color: #000000; padding: 10px 5px; border: 1px solid #000; font-weight: bold; text-transform: uppercase; font-size: 10px; } 
            
            td { padding: 8px 5px; border: 1px solid #666; vertical-align: middle; } 
            .text-center { text-align: center; } 
            .footer { margin-top: 30px; width: 100%; } 
            .ttd-box { float: right; width: 250px; text-align: center; }
        </style>
    </head>
    <body onload='window.print()'>
        <div style='text-align:center; border-bottom: 3px double #000; padding-bottom:10px;'>
            <h2>DAFTAR CUSTOMER PASANG BARU DGG CIREBON</h2>
            <h3>Periode: $namaBulan $tahun</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tgl Pasang</th>
                    <th>Nama Customer</th>
                    <th>Tipe Model</th>
                    <th>No Seri</th>
                    <th>Volt</th>
                    <th>Ctr Awal</th>
                    <th>Teknisi</th>
                    <th>Part</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>";

    if ($data->isEmpty()) {
        $html .= "<tr><td colspan='10' class='text-center' style='padding: 20px; color: #666; font-weight: bold;'>Tidak ada data pemasangan baru pada periode $namaBulan $tahun.</td></tr>";
    } else {
        foreach ($data as $index => $row) {
            $no = $index + 1;
            $tgl = date('d-m-Y', strtotime($row->created_at));

            $html .= "<tr>
                <td class='text-center'>$no</td>
                <td class='text-center'>$tgl</td>
                <td>{$row->customer->nama_customer}</td>
                <td>{$row->machine->tipe_model}</td>
                <td><b>{$row->machine->serial_number}</b></td>
                <td class='text-center'>{$row->volt} V</td>
                <td>BW: " . number_format($row->counter_bw) . " <br> CL: " . number_format($row->counter_color) . "</td>
                <td>{$row->technician->nama_technician}</td>
                <td>";

            // 🌟 JOIN LANGSUNG KE TABEL MASTER SPAREPART
            $parts = DB::table('deployment_sparepart')
                ->join('spareparts', 'deployment_sparepart.sparepart_id', '=', 'spareparts.id')
                ->where('deployment_sparepart.deployment_id', $row->id)
                ->select('spareparts.nama_sparepart', 'deployment_sparepart.jumlah')
                ->get();

            if ($parts->isNotEmpty()) {
                $html .= "<ul style='margin:0; padding-left:12px;'>";
                foreach ($parts as $p) {
                    $html .= "<li><b>" . strtoupper($p->nama_sparepart) . "</b> ({$p->jumlah} Pcs)</li>";
                }
                $html .= "</ul>";
            } else {
                $html .= "-";
            }

            $html .= "</td>
                <td>{$row->keterangan}</td>
            </tr>";
        }
    }

    $html .= "
            </tbody>
        </table>
        <div class='footer'>
            <div class='ttd-box'>
                <p>Indramayu, " . date('d F Y') . "</p>
                <p style='margin-bottom:60px;'>Admin Operasional,</p>
                <strong>( _________________________ )</strong>
            </div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.pemasangan');


// 2. CETAK SURAT JALAN (A5 LANDSCAPE)
Route::get('/cetak-surat-jalan/{id}', function ($id) {
    $d = Deployment::with(['machine', 'customer'])->findOrFail($id);
    $html = "<!DOCTYPE html><html><head><title>SJ - {$d->machine->serial_number}</title><style>@page {size: A5 landscape; margin: 0mm;} body { font-family: sans-serif; font-size: 11px; border: 2px solid #000; padding: 20px; } table { width: 100%; border-collapse: collapse; margin: 15px 0; } th, td { border: 1px solid #000; padding: 8px; } th { background: #f2f2f2; }</style></head><body onload='window.print()'><div style='display:flex; justify-content:space-between; border-bottom:2px solid #000; padding-bottom:5px;'><div><h2>PT. DINAMIKA GLOBAL GEMILANG</h2><p>SURAT JALAN</p></div><div style='text-align:right;'><p><b>Nomor: SJ/FC/CRB/" . date('dmy', strtotime($d->created_at)) . "/" . str_pad($d->id, 3, '0', STR_PAD_LEFT) . "</b></p><p>Tanggal: " . date('d-m-Y', strtotime($d->created_at)) . "</p></div></div><p><b>Penerima:</b> {$d->customer->nama_customer}<br><b>Alamat :</b> {$d->customer->alamat}</p><table><thead><tr><th>NO</th><th>NAMA BARANG / DESKRIPSI</th><th>SN / KODE PART</th><th>JUMLAH</th><th>KETERANGAN</th></tr></thead><tbody><tr><td style='text-align:center;'>1</td><td><b>Mesin Fotokopi {$d->machine->tipe_model}</b></td><td>{$d->machine->serial_number}</td><td>1 Unit</td><td></td></tr>";

    // 🌟 JOIN LANGSUNG KE TABEL MASTER SPAREPART
    $no = 2;
    $parts = DB::table('deployment_sparepart')->join('spareparts', 'deployment_sparepart.sparepart_id', '=', 'spareparts.id')->where('deployment_sparepart.deployment_id', $id)->select('spareparts.nama_sparepart', 'spareparts.code_part', 'deployment_sparepart.jumlah')->get();
    foreach ($parts as $p) {
        $html .= "<tr><td style='text-align:center;'>$no</td><td><b>" . strtoupper($p->nama_sparepart) . "</b></td><td>{$p->code_part}</td><td>{$p->jumlah} Pcs</td><td></td></tr>";
        $no++;
    }
    $html .= "</tbody></table><div style='display:flex; justify-content:space-around; margin-top:25px; text-align:center;'><div>Admin,<br><br><br>( ________ )</div><div>Disetujui,<br><br><br>( ________ )</div><div>Teknisi,<br><br><br>( ________ )</div><div>Penerima,<br><br><br>( ________ )</div></div></body></html>";
    return response($html);
})->name('cetak.surat-jalan');

// ---------------------------------

Route::get('/sparepart/report/outflow', function (Request $request) {

    $month = $request->query('month', date('m'));
    $year  = $request->query('year', date('Y'));

    $usages = ServiceLogSparepart::with([
        'sparepart',
        'serviceLog.machine.deployment.customer',
        'serviceLog.technician.rayon',
    ])
        ->whereHas('serviceLog', function ($q) use ($month, $year) {
            $q->whereMonth('tanggal', (int) $month)
                ->whereYear('tanggal', (int) $year);
        })
        ->get();

    $groupedUsages = $usages->groupBy(function ($item) {
        return optional(
            optional(
                optional($item->serviceLog)->technician
            )->rayon
        )->nama_rayon ?? 'TIDAK DIKETAHUI';
    });

    return view('print.sparepart-outflow')
        ->with('groupedUsages', $groupedUsages)
        ->with('month', $month)
        ->with('year', $year);
})->name('sparepart.report.outflow');


/* cetak-rekap-sparepart */


Route::get('/cetak-rekap-sparepart', function (Request $request) {

    $month = str_pad($request->query('bulan', date('m')), 2, '0', STR_PAD_LEFT);
    $year  = trim($request->query('tahun', date('Y')));

    $usages = \App\Models\ServiceLogSparepart::with([
        'sparepart',
        'serviceLog.machine.deployment.customer',
        'serviceLog.technician.rayon',
    ])
        ->whereHas('serviceLog', function ($q) use ($year, $month) {
            $q->whereYear('tanggal', $year)
                ->whereMonth('tanggal', (int) $month);
        })
        ->get();

    $groupedUsages = $usages->groupBy(function ($item) {
        return optional(
            optional(
                optional($item->serviceLog)->technician
            )->rayon
        )->nama_rayon ?? 'TIDAK DIKETAHUI';
    });

    return view('print.sparepart-outflow')
        ->with('groupedUsages', $groupedUsages)
        ->with('month', $month)
        ->with('year', $year);
})->name('cetak.rekap-sparepart');
