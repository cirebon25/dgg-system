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
use Illuminate\Support\Collection;
use App\Models\Technician;
use App\Http\Controllers\SaldoSparepartController;
use App\Http\Controllers\SparepartOutflowController;
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
// Route::get('/service-log/report/monthly', function (Request $request) {
//     $month = $request->query('month');
//     $year = $request->query('year');

//     // TARIK DATA LENGKAP TERMASUK DEPLOYMENT DAN SPAREPART
//     $logs = ServiceLog::whereYear('tanggal', $year)
//         ->whereMonth('tanggal', $month)
//         ->with(['machine.deployment.customer', 'technician', 'serviceLogSpareparts.sparepart'])
//         ->orderBy('tanggal', 'asc') // Urutkan dari tanggal terawal
//         ->get();

//     return view('print.monthly-report', [
//         'logs' => $logs,
//         'month' => $month,
//         'year' => $year,
//     ]);
// })->name('service-log.monthly');


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
 @page{ size: A4 landscape; margin: 10mm; }
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


// 1. ROUTE LAPORAN PEMASANGAN BARU 
Route::get(
    '/cetak-pemasangan-baru/{bulan?}/{tahun?}',
    [App\Http\Controllers\CetakPemasanganController::class, 'index']
)->name('cetak.pemasangan');


// Route::get('/sparepart/report/outflow', function (Request $request) {

//     $month = $request->query('month', date('m'));
//     $year  = $request->query('year', date('Y'));

//     $usages = ServiceLogSparepart::with([
//         'sparepart',
//         'serviceLog.machine.deployment.customer',
//         'serviceLog.technician.rayon',
//     ])
//         ->whereHas('serviceLog', function ($q) use ($month, $year) {
//             $q->whereMonth('tanggal', (int) $month)
//                 ->whereYear('tanggal', (int) $year);
//         })
//         ->get();

//     $groupedUsages = $usages->groupBy(function ($item) {
//         return optional(
//             optional(
//                 optional($item->serviceLog)->technician
//             )->rayon
//         )->nama_rayon ?? 'TIDAK DIKETAHUI';
//     });

//     return view('print.sparepart-outflow')
//         ->with('groupedUsages', $groupedUsages)
//         ->with('month', $month)
//         ->with('year', $year);
// })->name('sparepart.report.outflow');


// ROUTE OTOMATIS CETAK BUKTI NOTA PINJAM SPAREPART TEKNISI (DGG SYSTEM)
Route::get('/cetak-bukti-pinjam/{id}', function ($id) {

    // Tarik detail pinjaman part harian
    $loan = DB::table('part_borrowings') // sesuaikan dengan nama tabel pinjam part Akang (misal part_borrowings)
        ->join('spareparts', 'part_borrowings.sparepart_id', '=', 'spareparts.id')
        ->join('technicians', 'part_borrowings.technician_id', '=', 'technicians.id')
        ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')
        ->where('part_borrowings.id', $id)
        ->select(
            'part_borrowings.*',
            'spareparts.nama_sparepart',
            'spareparts.code_part',
            'technicians.nama_technician',
            DB::raw("COALESCE(rayons.nama_rayon, 'BARAT DAYA') as nama_rayon")
        )
        ->first();

    if (!$loan) {
        return "Bukti Transaksi Tidak Ditemukan!";
    }

    // Ambil saldo akhir stok teknisi saat ini setelah akumulasi
    $sisaSaldo = DB::table('technician_stocks')
        ->where('technician_id', $loan->technician_id)
        ->where('sparepart_id', $loan->sparepart_id)
        ->value('jumlah') ?? 0;

    return view('print.bukti-pinjam', compact('loan', 'sisaSaldo'));
})->name('cetak.bukti-pinjam');


// ROUTE FIX MUTLAK: CETAK SJ ROLLING KUSTOM PAYLOAD (ANTI-SESSION NULL)
Route::get('/cetak-sj-rolling', function (Request $request) {

    $payload = $request->query('payload');

    if (!$payload) {
        return 'Gagal memuat dokumen! Data Surat Jalan kosong. Silakan ulangi proses rolling dari menu Ganti Mesin, Boss Rudi.';
    }

    try {
        // Bongkar teks string Base64 kembali menjadi Array data riil ($d)
        $dataDecoded = json_decode(base64_decode($payload), true);

        if (!$dataDecoded) {
            return 'Struktur data Surat Jalan rusak, silakan input kembali.';
        }

        return view('cetak.surat-jalan-rolling', [
            'd'        => $dataDecoded,
            'tanggal'  => date('d/m/Y'),
            'nomor_sj' => 'SJ-RR/' . date('Ymd/Hi'),
        ]);
    } catch (\Exception $e) {
        return 'Eror Membaca Payload Data: ' . $e->getMessage();
    }
})->name('cetak.sj-rolling');


Route::get('/cetak-bukti-pinjam-multi/{id}', function ($id) {

    $header = DB::table('part_borrowing_headers')
        ->join('technicians', 'part_borrowing_headers.technician_id', '=', 'technicians.id')
        ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')
        ->where('part_borrowing_headers.id', $id)
        ->select(
            'part_borrowing_headers.*',
            'technicians.nama_technician',
            DB::raw("COALESCE(rayons.nama_rayon, 'PUSAT') as nama_rayon")
        )
        ->first();

    if (!$header) {
        return "Transaksi tidak ditemukan!";
    }

    $items = DB::table('part_borrowing_items')
        ->join('spareparts', 'part_borrowing_items.sparepart_id', '=', 'spareparts.id')
        ->where('part_borrowing_items.part_borrowing_header_id', $id)
        ->select(
            'part_borrowing_items.*',
            'spareparts.nama_sparepart',
            'spareparts.code_part'
        )
        ->get();

    // Ambil sisa saldo teknisi per item
    $saldoTeknisi = [];
    foreach ($items as $item) {
        $saldoTeknisi[$item->sparepart_id] = DB::table('technician_stocks')
            ->where('technician_id', $header->technician_id)
            ->where('sparepart_id', $item->sparepart_id)
            ->value('jumlah') ?? 0;
    }

    return view('print.bukti-pinjam-multi', compact('header', 'items', 'saldoTeknisi'));
})->name('cetak.bukti-pinjam-multi');


Route::get('/cetak-alokasi-mesin', function () {
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

    // Warna header atas untuk 4 rayon horizontal
    $rayonStyles = [
        0 => ['header_bg' => '#1e3a8a', 'text' => '#ffffff', 'kota_bg' => '#f0f9ff', 'kota_text' => '#0369a1'],
        1 => ['header_bg' => '#b45309', 'text' => '#ffffff', 'kota_bg' => '#fffbeb', 'kota_text' => '#92400e'],
        2 => ['header_bg' => '#047857', 'text' => '#ffffff', 'kota_bg' => '#f0fdf4', 'kota_text' => '#065f46'],
        3 => ['header_bg' => '#be185d', 'text' => '#ffffff', 'kota_bg' => '#fdf2f8', 'kota_text' => '#9d174d'],
    ];

    // --- STRATEGI PENYUSAUTAN BARIS (EQUAL HEIGHT BALANCING) ---
    // 1. Hitung total baris HTML yang akan dihasilkan oleh masing-masing rayon
    $rowCountPerRayon = [];
    foreach ($data as $namaRayon => $kotas) {
        $lines = 0;
        foreach ($kotas as $namaKota => $types) {
            $lines++; // 1 baris untuk Judul Kota
            $lines += count($types); // jumlah baris tipe mesin
            $lines++; // 1 baris untuk Total Kota
        }
        $rowCountPerRayon[$namaRayon] = $lines;
    }

    // 2. Cari tahu jumlah baris paling banyak di antara semua rayon
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

        // Loop Data Utama
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

        // 3. JIKA BELUM SAMA TINGGINYA, SUNTIKKAN DATA KOSONG DI SINI
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
})->name('cetak.alokasi');

// 2. CETAK SURAT JALAN (A5 LANDSCAPE)
Route::get('/cetak-surat-jalan/{id}', function ($id) {
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
                        <p style='margin:0; font-weight:bold; letter-spacing:1px;'>SURAT JALAN KIRIM MESIN BARU</p>
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
})->name('cetak.surat-jalan');



// ROUTE UTUH: HTML REALTIME CETAK SALDO GUDANG SPAREPART
// Route::get('/saldo-sparepart', function () {

//     // Tarik data realtime dari tabel spareparts
//     $spareparts = DB::table('spareparts')
//         ->orderByRaw("CASE WHEN no_part LIKE 'S%' THEN 0 ELSE 1 END")
//         ->orderBy('no_part', 'asc')
//         ->get();

//     $tanggalCetak = date('d/m/Y H:i');

//     return "
//     <!DOCTYPE html>
//     <html lang='id'>
//     <head>
//         <meta charset='UTF-8'>
//         <title>Cetak Saldo Sparepart - PT DGG</title>
//         <style>
//             body { font-family: 'Arial', sans-serif; color: #000; margin: 20px; padding: 0; }
//             .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
//             .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; letter-spacing: 1px; }
//             .header p { margin: 5px 0 0 0; font-size: 11px; color: #444; }
//             .meta-info { text-align: right; font-size: 11px; margin-bottom: 10px; font-weight: bold; }
//             table { width: 100%; border-collapse: collapse; margin-top: 5px; }
//             th { background-color: #f2f2f2 !important; border: 1px solid #000; padding: 10px 8px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
//             td { border: 1px solid #000; padding: 8px; font-size: 11px; vertical-align: middle; }
//             .text-center { text-align: center; }
//             .font-bold { font-weight: bold; }
//             .text-danger { color: red; font-weight: bold; }
            
//             /* Tombol Panel Atas */
//             .btn-area { background: #f4f4f5; padding: 12px; margin-bottom: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e4e4e7; }
//             .btn { padding: 6px 14px; border-radius: 4px; font-weight: bold; font-size: 12px; cursor: pointer; text-decoration: none; display: inline-block; }
//             .btn-print { background-color: #eab308; color: #000; border: 1px solid #ca8a04; }
//             .btn-close { background-color: #6b7280; color: #fff; border: none; }

//             @media print {
//                 .btn-area { display: none !important; }
//                 body { margin: 10mm; }
//             }
//         </style>
//     </head>
//     <body onload='window.print()'>

//         <div class='btn-area'>
//             <span style='font-size: 12px; color: #71717a; font-style: italic;'>💡 Halaman otomatis memicu cetak. Klik tombol jika dialog printer belum muncul.</span>
//             <div>
//                 <button onclick='window.print()' class='btn btn-print'>🖨️ Cetak Sekarang</button>
//                 <button onclick='window.close()' class='btn btn-close'>Tutup</button>
//             </div>
//         </div>

//         <div class='header'>
//             <h2>PT. DINAMIKA GLOBAL GEMILANG</h2>
//             <p>LAPORAN SALDO GUDANG SPAREPART REALTIME — DEPO CIREBON</p>
//         </div>

//         <div class='meta-info'>
//             Tanggal Cetak: {$tanggalCetak} WIB
//         </div>

//         <table>
//             <thead>
//                 <tr>
//                     <th width='5%' class='text-center'>No</th>
//                     <th width='20%' class='text-center'>NO PART</th>
//                     <th width='20%'>KODE PART</th>
//                     <th width='50%'>NAMA SPAREPART</th>
//                     <th width='20%' class='text-center'>SALDO GUDANG</th>
//                 </tr>
//             </thead>
//             <tbody>
//     " . (function () use ($spareparts) {
//         $htmlRows = "";
//         foreach ($spareparts as $index => $part) {
//             // Beri tanda warna merah menyala jika stok kosong (0)
//             $stokStyle = $part->stok <= 0 ? "class='text-danger'" : "class='font-bold'";
//             $nomorBaris = $index + 1;

//             // 🌟 FIX PERBAIKAN: Tanda petik diselaraskan dan penomoran PHP murni
//             $htmlRows .= "
//                 <tr>
//                     <td class='text-center'>{$nomorBaris}</td>
//                     <td class='font-bold text-center'>" . ($part->no_part) . "</td>
//                     <td><strong>" . ($part->code_part ?? '-') . "</strong></td>
//                     <td>" . strtoupper($part->nama_sparepart ?? '-') . "</td>
//                     <td class='text-center' {$stokStyle}>{$part->stok} Pcs</td>
//                 </tr>";
//         }
//         return $htmlRows ?: "<tr><td colspan='5' class='text-center' style='color:#999; padding:20px;'>Belum ada data sparepart di gudang.</td></tr>";
//     })() . "
//             </tbody>
//         </table>

//     </body>
//     </html>";
// })->name('saldo-sparepart');


// Cetak kartu stok per teknisi
Route::get('/cetak-kartu-stok/{technician_id}', function ($technician_id) {
    $teknisi = \App\Models\Technician::findOrFail($technician_id);
    $stocks  = \App\Models\TechnicianStock::with('sparepart')
        ->where('technician_id', $technician_id)
        ->where('jumlah', '>', 0)
        ->get();
    $total = $stocks->sum('jumlah');

    return view('print.kartu-stok', compact('teknisi', 'stocks', 'total'));
})->name('cetak.kartu-stok');

// Cetak kartu stok semua teknisi
Route::get('/cetak-kartu-stok-semua', function () {
    $data = \App\Models\Technician::with(['technicianStocks.sparepart'])
        ->whereHas('technicianStocks', fn($q) => $q->where('jumlah', '>', 0))
        ->get()
        ->map(fn($t) => [
            'teknisi' => $t,
            'stocks'  => $t->technicianStocks->where('jumlah', '>', 0),
            'total'   => $t->technicianStocks->where('jumlah', '>', 0)->sum('jumlah'),
        ]);

    return view('print.kartu-stok-semua', compact('data'));
})->name('cetak.kartu-stok-semua');


Route::get('/cetak-surat-penarikan/{id}', function ($id) {
    $withdrawal = \App\Models\MachineWithdrawal::with(['machine', 'customer'])->findOrFail($id);
    return view('print.surat-penarikan', compact('withdrawal'));
})->name('cetak.surat-penarikan');

// Rekap pengeluaran part teknisi
Route::get('/cetak-rekap-sparepart', function (Request $request) {

    $month = str_pad($request->query('bulan', date('m')), 2, '0', STR_PAD_LEFT);
    $year  = trim($request->query('tahun', date('Y')));

    $masterUsages = collect();

    /*
    |--------------------------------------------------------------------------
    | KANAL 1 — SERVICE LOG SPAREPART
    |--------------------------------------------------------------------------
    */

    $serviceLogs = DB::table('service_logs')

        ->leftJoin('customers', 'service_logs.customer_id', '=', 'customers.id')

        ->leftJoin('machines', 'service_logs.machine_id', '=', 'machines.id')

        ->leftJoin('technicians', 'service_logs.technician_id', '=', 'technicians.id')

        ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')

        // DETAIL SPAREPART SERVICE
        ->join(
            'service_log_spareparts',
            'service_logs.id',
            '=',
            'service_log_spareparts.service_log_id'
        )

        // MASTER SPAREPART
        ->join(
            'spareparts',
            'service_log_spareparts.sparepart_id',
            '=',
            'spareparts.id'
        )

        ->whereYear('service_logs.tanggal', $year)

        ->whereMonth('service_logs.tanggal', (int) $month)

        ->select([

            'service_logs.tanggal',

            'customers.nama_customer',

            'machines.tipe_model',
            'machines.serial_number',

            'spareparts.nama_sparepart',
            'spareparts.nama_alias',

            // QTY SPAREPART
            'service_log_spareparts.jumlah',

            'service_logs.usage_bw',
            'service_logs.usage_color',

            'service_logs.counter_bw',
            'service_logs.counter_color',

            'technicians.nama_technician',

            'rayons.nama_rayon'
        ])

        ->orderBy('service_logs.tanggal', 'asc')

        ->get();

    foreach ($serviceLogs as $log) {

        $masterUsages->push((object)[

            'tanggal' => $log->tanggal,

            'nama_customer' => $log->nama_customer ?? '-',

            'tipe_model' => $log->tipe_model ?? '-',

            'serial_number' => $log->serial_number ?? '-',

            'nama_part' => ($log->nama_alias ? $log->nama_alias : ($log->nama_sparepart ?? '-')) . ' (' . ($log->jumlah ?? 0) . ' pcs)',
            'usage_bw' => $log->usage_bw ?? 0,

            'usage_color' => $log->usage_color ?? 0,

            'counter_bw' => $log->counter_bw ?? 0,

            'counter_color' => $log->counter_color ?? 0,

            'nama_technician' => $log->nama_technician ?? '-',

            'nama_rayon' => $log->nama_rayon ?? 'WILAYAH LAIN'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | KANAL 2 — DEPLOYMENT SPAREPART
    |--------------------------------------------------------------------------
    */

    $deploymentLogs = DB::table('deployments')

        ->leftJoin('customers', 'deployments.customer_id', '=', 'customers.id')

        ->leftJoin('machines', 'deployments.machine_id', '=', 'machines.id')

        ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')

        ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')

        // DETAIL DEPLOYMENT SPAREPART
        ->join(
            'deployment_sparepart',
            'deployments.id',
            '=',
            'deployment_sparepart.deployment_id'
        )

        // MASTER SPAREPART
        ->join(
            'spareparts',
            'deployment_sparepart.sparepart_id',
            '=',
            'spareparts.id'
        )

        ->whereYear('deployments.tanggal_instal', $year)

        ->whereMonth('deployments.tanggal_instal', (int) $month)

        ->select([

            'deployments.tanggal_instal as tanggal',

            'customers.nama_customer',

            'machines.tipe_model',
            'machines.serial_number',

            'spareparts.nama_sparepart',
            'spareparts.nama_alias',

            // QTY
            'deployment_sparepart.jumlah',

            'deployments.counter_bw',
            'deployments.counter_color',

            'technicians.nama_technician',

            'rayons.nama_rayon'
        ])

        ->orderBy('deployments.tanggal_instal', 'asc')

        ->get();

    foreach ($deploymentLogs as $log) {

        $masterUsages->push((object)[

            'tanggal' => $log->tanggal,

            'nama_customer' => $log->nama_customer ?? '-',

            'tipe_model' => $log->tipe_model ?? '-',

            'serial_number' => $log->serial_number ?? '-',

            'nama_part' => ($log->nama_alias ? $log->nama_alias : ($log->nama_sparepart ?? '-')) . ' (' . ($log->jumlah ?? 0) . ' pcs) [New Install]',


            'usage_bw' => 0,

            'usage_color' => 0,

            'counter_bw' => $log->counter_bw ?? 0,

            'counter_color' => $log->counter_color ?? 0,

            'nama_technician' => $log->nama_technician ?? '-',

            'nama_rayon' => $log->nama_rayon ?? 'WILAYAH LAIN'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GROUPING RAYON
    |--------------------------------------------------------------------------
    */

    $groupedUsages = $masterUsages
        ->sortBy('tanggal')
        ->groupBy('nama_rayon');

    return view('print.sparepart-outflow')
        ->with('groupedUsages', $groupedUsages)
        ->with('month', $month)
        ->with('year', $year);
})->name('cetak.rekap-sparepart');



// kinerja teknisi per bulan: total kunjungan + breakdown tipe kunjungan (service, instalasi, penarikan, dll)
Route::get('/print/technician-performance', function (Illuminate\Http\Request $request) {
    $month = $request->month ?? date('m');
    $year = $request->year ?? date('Y');

    // Ambil semua teknisi
    $technicians = Technician::all();
    $reportData = [];

    foreach ($technicians as $tech) {
        $logs = ServiceLog::where('technician_id', $tech->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        $totalVisits = $logs->count();

        // Hitung per tipe kunjungan
        $breakdown = $logs->groupBy('tipe_kunjungan')->map(function ($items) use ($totalVisits) {
            $count = $items->count();
            return [
                'count' => $count,
                'percentage' => $totalVisits > 0 ? round(($count / $totalVisits) * 100, 1) : 0
            ];
        });

        if ($totalVisits > 0) {
            $reportData[] = (object)[
                'nama' => $tech->nama_technician,
                'total' => $totalVisits,
                'details' => $breakdown
            ];
        }
    }

    return view('print.technician-performance', compact('reportData', 'month', 'year'));
})->name('print.tech-performance');


// kinerja rayon per bulan: total kunjungan + breakdown tipe kunjungan (service, instalasi, penarikan, dll)
Route::get('/print/performance-rayon', function (Illuminate\Http\Request $request) {
    $month = $request->month ?? date('m');
    $year  = $request->year  ?? date('Y');

    $technicians = \App\Models\Technician::with(['rayon', 'serviceLogs' => function ($query) use ($month, $year) {
        $query->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->with('customer'); // <-- tambah ini
    }])->get();

    $tipeKolom = ['CM', 'RM', 'RN', 'RR', 'Mesin', 'RM Tertunda'];

    $hariKerja = 0;
    $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $day = \Carbon\Carbon::create($year, $month, $d)->dayOfWeek;
        if ($day !== \Carbon\Carbon::SUNDAY) $hariKerja++;
    }

    $reportData = $technicians->groupBy(fn($t) => $t->rayon->nama_rayon ?? 'Tanpa Rayon');

    return view('print.performance-rayon', compact('reportData', 'month', 'year', 'tipeKolom', 'hariKerja'));
})->name('print.performance-rayon');


Route::get('/withdrawal/rekap', function (Request $request) {
    $month = $request->query('month', date('m'));
    $year  = $request->query('year',  date('Y'));

    $records = \App\Models\MachineWithdrawal::with(['machine', 'customer'])
        ->whereMonth('tanggal_tarik', (int) $month)
        ->whereYear('tanggal_tarik',  (int) $year)
        ->orderBy('tanggal_tarik', 'asc')
        ->get();

    $namaBulan = \Carbon\Carbon::createFromFormat('m', $month)->translatedFormat('F');

    return view('print.withdrawal-rekap', compact('records', 'namaBulan', 'month', 'year'));
})->name('withdrawal.rekap')->middleware('auth');


// tracking sparepart
// Riwayat Ganti Part - Cetak PDF
Route::get('/cetak/part-per-mesin', [\App\Http\Controllers\PartReplacementController::class, 'cetakPerMesin'])->name('cetak.part.mesin');
Route::get('/cetak/part-per-bulan', [\App\Http\Controllers\PartReplacementController::class, 'cetakPerBulan'])->name('cetak.part.bulan');

// Ganti route lama di routes/web.php dengan ini:
Route::get(
    '/cetak-tukar-guling',
    [App\Http\Controllers\CetakSwapController::class, 'index']
)->name('cetak.swap');


// REALTIME CETAK SALDO GUDANG SPAREPART
Route::get('/saldo-sparepart', [SaldoSparepartController::class, 'index'])->name('saldo-sparepart');




Route::get('/sparepart/report/outflow', [SparepartOutflowController::class, 'index'])
    ->name('sparepart.report.outflow');


// Route::get('/sparepart/report/outflow', function (Request $request) {

//     $month = $request->query('month', date('m'));
//     $year  = $request->query('year', date('Y'));

//     $usages = ServiceLogSparepart::with([
//         'sparepart',
//         'serviceLog',
//         'serviceLog.machine',
//         'serviceLog.machine.deployment',
//         'serviceLog.machine.deployment.customer',
//         'serviceLog.technician',
//         'serviceLog.technician.rayon',
//     ])
//         ->whereHas('serviceLog', function ($q) use ($month, $year) {
//             $q->whereMonth('tanggal', (int) $month)
//               ->whereYear('tanggal', (int) $year);
//         })
//         ->get();

//     // Flatten: ubah setiap relasi ke object flat agar mudah diakses di blade
//     $flat = $usages->map(function ($item) {
//         $log        = $item->serviceLog;
//         $machine    = optional($log)->machine;
//         $deploy     = optional($machine)->deployment;
//         $customer   = optional($deploy)->customer;
//         $technician = optional($log)->technician;
//         $rayon      = optional($technician)->rayon;

//         return (object) [
//             // Data kunjungan
//             'tanggal'          => optional($log)->tanggal,
//             'nama_customer'    => optional($customer)->nama_customer
//                                   ?? optional($customer)->nama  // fallback nama kolom
//                                   ?? '-',
//             'tipe_model'       => optional($machine)->tipe_model
//                                   ?? optional($machine)->tipe
//                                   ?? '-',
//             'serial_number'    => optional($machine)->serial_number
//                                   ?? optional($machine)->no_seri
//                                   ?? '-',
//             // Counter & usage — sesuaikan nama kolom jika berbeda
//             'usage_bw'         => optional($log)->usage_bw    ?? optional($log)->pemakaian_bw    ?? 0,
//             'usage_color'      => optional($log)->usage_color ?? optional($log)->pemakaian_color ?? 0,
//             'counter_bw'       => optional($log)->counter_bw  ?? optional($log)->counter_akhir_bw ?? 0,
//             'counter_color'    => optional($log)->counter_color ?? optional($log)->counter_akhir_color ?? 0,
//             // Sparepart
//             'nama_part'        => optional($item->sparepart)->nama_sparepart
//                                   ?? optional($item->sparepart)->nama_part
//                                   ?? '-',
//             // Teknisi & rayon
//             'nama_technician'  => optional($technician)->nama_technician
//                                   ?? optional($technician)->nama
//                                   ?? '-',
//             'nama_rayon'       => optional($rayon)->nama_rayon
//                                   ?? optional($rayon)->nama
//                                   ?? 'TIDAK DIKETAHUI',
//         ];
//     });

//     // Group by rayon
//     $groupedUsages = $flat->groupBy('nama_rayon');

//     return view('print.sparepart-outflow', compact('groupedUsages', 'month', 'year'));

// })->name('sparepart.report.outflow'); 


Route::get('/cetak-surat-retur/{id}', function ($id) {
    $retur = \App\Models\MachineReturn::with(['machine'])->findOrFail($id);
    return view('print.surat-retur', compact('retur'));
})->name('cetak.surat-retur')->middleware('auth');