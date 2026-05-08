<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceLog;
use Illuminate\Http\Request;
use App\Models\ServiceLogSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\Machine;
use App\Models\Deployment;
use App\Models\Sparepart;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Rayon;
use Carbon\Carbon;
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

Route::get('/print-service-bulk', function (Illuminate\Http\Request $request) {
    // Ambil ID yang dikirim dari tombol centang di Filament
    $ids = explode(',', $request->ids);
    $records = App\Models\ServiceLog::with(['machine', 'technician', 'sparepart'])
                ->whereIn('id', $ids)
                ->orderBy('tanggal', 'asc')
                ->get();

    return view('print-service-bulk', compact('records'));
})->name('print.service.bulk')->middleware('auth');

// / Jalur khusus untuk cetak service log
Route::get('/service-log/{record}/print', function (ServiceLog $record) {
    return view('print.service-log', ['record' => $record]);
})->name('service-log.print');



// Route::get('/service-log/report/monthly', function (Request $request) {
//     $month = $request->query('month');
//     $year = $request->query('year');

//     $logs = ServiceLog::whereYear('tanggal', $year)
//         ->whereMonth('tanggal', $month)
//         ->with(['machine.customer', 'technician'])
//         ->get();

//     return view('print.monthly-report', [
//         'logs' => $logs,
//         'month' => $month,
//         'year' => $year
//     ]);
// })->name('service-log.monthly');

// ROUTE CETAK BULANAN 
// Route::get('/service-log/report/monthly', function (Request $request) {
//     $month = $request->query('month');
//     $year = $request->query('year');

//     // Ambil data servis berdasarkan bulan dan tahun
//     $logs = ServiceLog::whereYear('tanggal', $year)
//         ->whereMonth('tanggal', $month)
//         ->with(['machine.customer', 'technician'])
//         ->get();

//     return view('print.monthly-report', [
//         'logs' => $logs,
//         'month' => $month,
//         'year' => $year
//     ]);
// })->name('service-log.monthly');

// ROUTE CETAK BULANAN
Route::get('/service-log/report/monthly', function (Illuminate\Http\Request $request) {
    $month = $request->query('month');
    $year = $request->query('year');

    // TARIK DATA LENGKAP TERMASUK DEPLOYMENT DAN SPAREPART
    $logs = App\Models\ServiceLog::whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->with(['machine.deployment.customer', 'technician', 'serviceLogSpareparts.sparepart'])
        ->orderBy('tanggal', 'asc') // Urutkan dari tanggal terawal
        ->get();

    return view('print.monthly-report', [
        'logs' => $logs,
        'month' => $month,
        'year' => $year
    ]);
})->name('service-log.monthly');

Route::get('/sparepart/report/outflow', function (Illuminate\Http\Request $request) {
    $month = $request->query('month');
    $year = $request->query('year');

    $usages = \App\Models\ServiceLogSparepart::whereHas('serviceLog', function($q) use ($month, $year) {
        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
    })->with(['sparepart', 'serviceLog.machine.deployment.customer'])->get();

    return view('print.sparepart-outflow', compact('usages', 'month', 'year'));
})->name('sparepart.report.outflow');


Route::get('/sparepart/monitor-umur/{machine_id}', function ($machine_id) {
    $machine = App\Models\Machine::with(['deployment.customer', 'serviceLogs'])->findOrFail($machine_id);
    
    // Ambil semua histori pergantian sparepart di mesin ini
    $partHistories = App\Models\ServiceLogSparepart::whereHas('serviceLog', function($q) use ($machine_id) {
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

Route::get('/cetak-tukar-guling', function () {
    // Kueri diperbaiki: Service Log -> Machine -> Deployment -> Customer
    $data = DB::table('service_logs')
        ->join('machines', 'service_logs.machine_id', '=', 'machines.id')
        ->join('deployments', 'machines.id', '=', 'deployments.machine_id') // Jembatan ke Customer
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->join('technicians', 'service_logs.technician_id', '=', 'technicians.id')
        ->where('service_logs.perbaikan', 'LIKE', '%Tukar Guling%') 
        ->select(
            'service_logs.tanggal',
            'customers.nama_customer',
            'customers.kota',
            'machines.serial_number as sn_lama',
            'machines.tipe_model as tipe_lama',
            'service_logs.perbaikan as unit_pengganti', 
            'technicians.nama_technician'
        )
        ->orderBy('service_logs.tanggal', 'desc')
        ->get();

    // Bagian HTML ke bawah tetap sama seperti sebelumnya...
    // (Gunakan kode HTML Full yang sudah saya berikan di pesan sebelumnya)

    $html = "
    <html>
    <head>
        <title>Laporan Tukar Guling DGG</title>
        <style>
            body { font-family: sans-serif; font-size: 11px; padding: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #000; padding: 6px; text-align: left; }
            th { background-color: #f3f4f6; text-transform: uppercase; }
            .header-box { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
            .badge-out { color: #dc2626; font-weight: bold; }
            .badge-in { color: #16a34a; font-weight: bold; }
            .text-center { text-align: center; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header-box'>
            <h1 style='margin:0;'>DGG SYSTEM - OPERATIONAL HUB</h1>
            <h2 style='margin:5px 0;'>BERITA ACARA & LAPORAN TUKAR GULING (SWAP)</h2>
            <p>Periode Laporan: " . date('M Y') . "</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width='80'>Tanggal</th>
                    <th>Customer / Wilayah</th>
                    <th>Unit LAMA (Ditarik)</th>
                    <th>Unit BARU (Terpasang)</th>
                    <th>Teknisi</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($data as $row) {
        $html .= "
        <tr>
            <td class='text-center'>" . date('d/m/Y', strtotime($row->tanggal)) . "</td>
            <td>
                <strong>$row->nama_customer</strong><br>
                <small>$row->kota</small>
            </td>
            <td>
                <span class='badge-out'>[OUT]</span> $row->sn_lama<br>
                <small>$row->tipe_lama</small>
            </td>
            <td>
                <span class='badge-in'>[IN]</span> $row->unit_pengganti
            </td>
            <td>$row->nama_technician</td>
        </tr>";
    }

    $html .= "
            </tbody>
        </table>
        <div style='margin-top: 30px; float: right; width: 200px; text-align: center;'>
            <p>Indramayu, " . date('d M Y') . "</p>
            <br><br><br>
            <strong>( ________________ )</strong><br>
            <p>Admin Operasional</p>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.swap');

Route::get('/cetak-pemasangan-baru/{bulan}/{tahun}', function ($bulan, $tahun) {
    // 1. AMBIL DATA MENGGUNAKAN ELOQUENT MODEL (Jauh lebih pintar & otomatis)
    $data = \App\Models\Deployment::with(['machine', 'customer.rayon', 'technician', 'spareparts'])
        ->whereMonth('created_at', $bulan)
        ->whereYear('created_at', $tahun)
        ->orderBy('created_at', 'asc')
        ->get();

    $bulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $namaBulan = $bulanIndo[$bulan] ?? 'Tidak Diketahui';

    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Laporan Pemasangan Baru - DGG System</title>
        <style>
            @page { size: landscape; margin: 10mm; }
            body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
            .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
            .header h1 { margin: 0; font-size: 20px; color: #1e40af; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #1e40af; color: white; text-transform: uppercase; padding: 10px 5px; border: 1px solid #000; }
            td { padding: 8px 5px; border: 1px solid #666; vertical-align: middle; }
            tr:nth-child(even) { background-color: #f8fafc; }
            .text-center { text-align: center; }
            .font-bold { font-weight: bold; }
            .footer { margin-top: 30px; width: 100%; }
            .ttd-box { float: right; width: 250px; text-align: center; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header'>
            <h1>DGG SYSTEM - OPERATIONAL HUB</h1>
            <h2>LAPORAN PEMASANGAN UNIT MESIN BARU</h2>
            <p>Periode: $namaBulan $tahun</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width='30'>No</th>
                    <th width='80'>Tgl Pasang</th>
                    <th width='110'>SN Mesin</th>
                    <th width='110'>Tipe Model</th>
                    <th>Nama Customer</th>
                    <th width='120'>Wilayah / Rayon</th>
                    <th width='100'>Counter (BW/CL)</th>
                    <th width='50'>Volt</th>
                    <th width='100'>Teknisi</th>
                    <th width='140'>Sparepart</th>
                </tr>
            </thead>
            <tbody>";

    if ($data->isEmpty()) {
        $html .= "<tr><td colspan='10' class='text-center'>Tidak ada data pemasangan baru pada periode ini.</td></tr>";
    } else {
        foreach ($data as $index => $row) {
            $no = $index + 1;
            $tgl = date('d-m-Y', strtotime($row->created_at));
            
            // Mengambil data relasi dengan aman menggunakan tanda tanya (?) milik Laravel mencegah eror null
            $sn = $row->machine->serial_number ?? '-';
            $model = $row->machine->tipe_model ?? '-';
            $customer = $row->customer->nama_customer ?? '-';
            $kota = $row->customer->kota ?? '-';
            $rayon = $row->customer->rayon->nama_rayon ?? '-';
            
            $bw = isset($row->counter_bw) ? number_format($row->counter_bw) : '0';
            $cl = isset($row->counter_color) ? number_format($row->counter_color) : '0';
            $voltase = !empty($row->volt) ? $row->volt . ' V' : '-';
            $teknisi = $row->technician->nama_technician ?? '-';

            $html .= "
            <tr>
                <td class='text-center'>$no</td>
                <td class='text-center'>$tgl</td>
                <td class='font-bold'>$sn</td>
                <td>$model</td>
                <td>$customer</td>
                <td>$kota ($rayon)</td>
                <td>BW: $bw <br> CL: $cl</td>
                <td class='text-center'>$voltase</td>
                <td>$teknisi</td>
                <td>";

            // 💡 SEKARANG AMBIL DATA SPAREPART LANGSUNG DARI RELASI MODEL (Pasti Tembus!)
            if ($row->spareparts && $row->spareparts->isNotEmpty()) {
                $html .= "<ul style='margin:0; padding-left:12px;'>";
                foreach ($row->spareparts as $sp) {
                    $html .= "<li>{$sp->nama_sparepart} ({$sp->pivot->jumlah} Pcs)</li>";
                }
                $html .= "</ul>";
            } else {
                $html .= "<span style='color: #999;'>-</span>";
            }

            $html .= "</td>
            </tr>";
        }
    }

    $html .= "
            </tbody>
        </table>

        <div class='footer'>
            <div class='ttd-box'>
                <p>Indramayu, " . date('d F Y') . "</p>
                <p style='margin-bottom: 60px;'>Admin Operasional,</p>
                <strong>( _________________________ )</strong>
            </div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.pemasangan');

Route::get('/cetak-surat-jalan/{id}', function ($id) {
    // KUNCI UTAMA: Kita panggil 'with spareparts' agar datanya ikut keambil
    $d = Deployment::with(['machine', 'customer', 'spareparts'])->findOrFail($id);
    // dd($d->spareparts->toArray());

    // Logika Merk
    $tipe = strtoupper($d->machine->tipe_model);
    $merk = 'Mesin Fotokopi';
    if (str_contains($tipe, 'IR') || str_contains($tipe, 'IRA') || str_contains($tipe, 'MF')){
        $merk = "Mesin Fotokopi Canon";
    } elseif (str_contains($tipe, 'M ') || str_contains($tipe, 'ECOSYS') || str_contains($tipe, 'KYOCERA')) {
        $merk = "Mesin Fotokopi Kyocera";
    } elseif (str_contains($tipe, 'SINDOH') || str_contains($tipe, 'D') || str_contains($tipe, 'C')) {
        $merk = "Mesin Fotokopi Sindoh";
    }

    $bulanData = date('m', strtotime($d->created_at));
    $tahunData = date('Y', strtotime($d->created_at));

    // Rumus menghitung jumlah surat jalan khusus di bulan & tahun ini saja (Reset tiap bulan)
    $urutanBulan = DB::table('deployments')
        ->whereMonth('created_at', $bulanData)
        ->whereYear('created_at', $tahunData)
        ->where('id', '<=', $d->id)
        ->count();

    $tahunData = date('Y', strtotime($d->created_at));

// // Hitung urutan khusus untuk tahun ini saja (Reset tiap 1 Januari)
// $urutanTahun = DB::table('deployments')
//     ->whereYear('created_at', $tahunData)
//     ->where('id', '<=', $d->id)
//     ->count();

// $html .= "
// <div style='text-align: right;'>
//     <p style='margin:0;'><b>Nomor: SJ/FC/CRB/" . date('dmy', strtotime($d->created_at)) . "/" . str_pad($urutanTahun, 3, '0', STR_PAD_LEFT) . "</b></p>
//     <p style='margin:0;'>Tanggal: " . date('d-m-Y', strtotime($d->created_at)) . "</p>
// </div>";

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>SJ - {$d->machine->serial_number}</title>
        <style>
            @page {size: A5 landscape; margin: 0mm;}
            body { font-family: sans-serif; font-size: 11px; border: 2px solid #000; padding: 20px; }
            .header { border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px; display: flex; justify-content: space-between; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background: #f2f2f2; text-transform: uppercase; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header'>
            <div>
                <h2 style='margin:0;'>PT. DINAMIKA GLOBAL GEMILANG</h2>
                <p style='margin:0;'>Depo Cirebon - SURAT JALAN</p>
            </div>
            <div style='text-align: right;'>
                <p style='margin:0;'><b>Nomor: SJ/FC/CRB/" . date('dmy', strtotime($d->created_at)) . "/" . str_pad($d->id, 3, '0', STR_PAD_LEFT) . "</b></p>
                <p style='margin:0;'>Tanggal: " . date('d-m-Y', strtotime($d->created_at)) . "</p>
            </div>
        </div>
        
        <p>
          <b>Penerima:</b> {$d->customer->nama_customer} 
          <br></br>
            <br> <b>Alamat :</b> {$d->customer->alamat} - {$d->customer->kota} <br/>
        </p>

       <table style='width: 100%; height: 80mm; border-collapse: collapse;'>
            <thead>
                <tr>
                    <th width='10'>NO</th>
                    <th width='100'>NAMA BARANG / DESKRIPSI</th>
                    <th width='70'>SN / KODE PART</th>
                    <th width='60'>JUMLAH</th>
                    <th width='50'>KETERANGAN / COUNTER</th>
                </tr>
            </thead>
            <tbody>
                <tr style='height: 30px;'>
                    <td style='text-align: center;'>1</td>
                    <td><b>$merk {$d->machine->tipe_model}</b></td>
                    <td>{$d->machine->serial_number}</td>
                    <td>1 Unit</td>
                    <td> </td>
                </tr>";

    // --- BAGIAN INI YANG MENAMPILKAN SPAREPART ---
    $no = 2;
    foreach ($d->spareparts as $part) {
        //  3. Kunci juga tinggi baris sparepart (30px)
        $html .= "<tr style='height: 30px;'>
            <td style='text-align: center;'>$no</td>
            <td>{$part->nama_sparepart}</td>
            <td>" . ($part->code_part ?: $part->no_part ?: '-') . "</td>
            <td>{$part->pivot->jumlah} Pcs</td>
            <td> </td>
        </tr>";
        $no++;
    }

    // 💡 4. TRIK UTAMA: Tambahkan baris kosong tanpa ukuran (height: auto) sebelum tutup tbody.
    // Baris ini akan melar otomatis menyerap sisa ruang kertas dan menarik semua garis kolom lurus ke bawah!
    $html .= "
                <tr style='height: auto;'>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div style='display: flex; justify-content: space-around; margin-top: 25px; text-align: center;'>
            <div>Admin,<br><br><br>( ____________ )</div>
            <div>Disetujui,<br><br><br>( ____________ )</div>
            <div>Teknisi,<br><br><br>( ____________ )</div>
            <div>Penerima,<br><br><br>( ____________ )</div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.surat-jalan');



// FITUR 2: LAPORAN ALOKASI CUSTOMER (Mesin yang sedang terpasang)
Route::get('/cetak-alokasi-customer', function () {
    $data = \App\Models\Deployment::with(['customer', 'machine'])->get();

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
                <tr>                    <th>NAMA CUSTOMER</th>
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


Route::get('/cetak-rekap-sparepart', function (Request $request) {
    // 1. Ambil data stok sparepart urut abjad
    $spareparts = Sparepart::orderBy('nama_sparepart', 'asc')->get();
    
    // 2. Pengaturan Periode
    $bulanNominal = $request->query('bulan', date('m'));
    $namaBulan = date('F', mktime(0, 0, 0, $bulanNominal, 10));
    $tahun = $request->query('tahun', date('Y'));

    $html = "
    <html>
    <head>
        <title></title>
        <style>
            body { font-family: sans-serif; font-size: 11px; padding: 10px; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .header p { margin: 2px 0; font-weight: bold; }
            .judul { font-size: 16px; margin-top: 10px; font-weight: bold; text-transform: uppercase; }
            
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: center; }
            th { background: #f2f2f2; text-transform: uppercase; font-size: 10px; }
            
            .text-left { text-align: left; }
            .font-bold { font-weight: bold; }
            
            /* Menghilangkan margin otomatis saat print agar lebih bersih */
            @media print { 
                @page { margin: 1cm; }
                * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } 
            }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header'>
            <p>PT. DINAMIKA GLOBAL GEMILANG</p>
            <p>DEPO CIREBON</p>
            <div class='judul'>REKAP SALDO SPAREPART</div>
            <p>PERIODE: $namaBulan $tahun</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width='30'>NO</th>
                    <th width='100'>KODE PART</th>
                    <th width='100'>NO PART</th>
                    <th>NAMA SPAREPART</th>
                    <th width='80'>STOK</th>
                </tr>
            </thead>
            <tbody>";

    $no = 1;
    foreach ($spareparts as $s) {
        $html .= "<tr>
            <td>$no</td>
            <td>" . ($s->code_part ?: '-') . "</td>
            <td>" . ($s->no_part ?: '-') . "</td>
            <td class='text-left'>" . ($s->nama_sparepart ?: '-') . "</td>
            <td class='font-bold' style='font-size: 12px;'>" . ($s->stok ?? 0) . " Unit</td>
        </tr>";
        $no++;
    }

    $html .= "
            </tbody>
        </table>

        <div style='margin-top: 40px; float: right; text-align: center; width: 250px;'>
            <p>Cirebon, " . date('d-m-Y') . "</p>
            <br><br><br><br>
            <p><b>( _________________ )</b></p>
            <p>Admin Gudang</p>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.rekap-sparepart');


// ➡️ RUTE 1: UNTUK MENCETAK STIKER QR CODE (Ukuran Presisi Stiker)
Route::get('/mesin/{id}/cetak-qr', function ($id) {
    // Pastikan pakai try-catch biar kalau ID mesin gak ada gak langsung eror putih
    try {
        $machine = \App\Models\Machine::findOrFail($id);
    } catch (\Exception $e) {
        return "Data mesin tidak ditemukan!";
    }
    
    // URL yang akan dibuka saat scan (Histori Mesin)
    $urlHistori = route('mesin.histori', ['id' => $machine->id]);

    // Membuat gambar QR Code (Pakai format SVG agar tajam saat di-print)
    // Jangan lupa import: use SimpleSoftwareIO\QrCode\Facades\QrCode; di atas file routes
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
    $machine = \App\Models\Machine::findOrFail($id);

    // 1. Ambil Riwayat Mutasi / Penempatan Mesin (Deployments)
    $deployments = DB::table('deployments')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')
        ->where('machine_id', $id)
        ->select('deployments.*', 'customers.nama_customer', 'customers.kota', 'technicians.nama_technician')
        ->orderBy('created_at', 'desc')
        ->get();

    // 2. Ambil Riwayat Service/Perbaikan (Log Service)
    // Di-protect pakai try-catch biar aman kalau nama tabel log service Akang agak beda
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
        <script src='https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4'></script>
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
                                <div><b>Counter Awal:</b><br>BW: ".number_format($dep->counter_bw)."<br>CL: ".number_format($dep->counter_color)."</div>
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


// --- ROUTE CETAK STOK GUDANG SPAREPART ---
Route::get('/cetak-stok-gudang', function () {
    // 1. Ambil semua data sparepart, urutkan dari A ke Z
    $spareparts = \App\Models\Sparepart::orderBy('nama_sparepart', 'asc')->get();

    // 2. Susunan HTML Laporan
    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Laporan Stok Gudang - DGG System</title>
        <style>
            body { font-family: sans-serif; font-size: 12px; padding: 20px; color: #333; }
            header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
            header h1 { margin: 0; color: #1e40af; font-size: 20px; }
            header h2 { margin: 5px 0; font-size: 16px; }
            
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #1e40af; color: white; padding: 10px; border: 1px solid #000; text-transform: uppercase; }
            td { padding: 8px; border: 1px solid #000; }
            tr:nth-child(even) { background-color: #f8fafc; }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            
            @media print {
                @page { size: portrait; margin: 10mm; }
                body { padding: 0; }
            }
        </style>
    </head>
    <body onload='window.print()'>
        <header>
            <h1>DGG SYSTEM - WAREHOUSE HUB</h1>
            <h2>LAPORAN STOK GUDANG SPAREPART</h2>
            <p>Per Tanggal: " . date('d-m-Y H:i') . "</p>
        </header>

        <table>
            <thead>
                <tr>
                    <th width='40'>NO</th>
                    <th>NAMA SPAREPART</th>
                    <th width='150'>KODE / NO PART</th>
                    <th width='100'>JUMLAH STOK</th>
                </tr>
            </thead>
            <tbody>";

    // 3. Looping Data Sparepart
    if ($spareparts->isEmpty()) {
        $html .= "<tr><td colspan='4' class='text-center'>Data sparepart kosong.</td></tr>";
    } else {
        foreach ($spareparts as $index => $item) {
            $no = $index + 1;
            $noPart = $item->no_part ?: ($item->code_part ?: '-');
            // Jika stok sedikit (misal di bawah 5), kasih warna merah buat peringatan
            $warnaStok = ($item->stok <= 5) ? "color: red; font-weight: bold;" : "";
            
            $html .= "
            <tr>
                <td class='text-center'>$no</td>
                <td class='font-bold'>{$item->nama_sparepart}</td>
                <td class='text-center'>$noPart</td>
                <td class='text-center' style='$warnaStok'>{$item->stok} Pcs</td>
            </tr>";
        }
    }

    $html .= "
            </tbody>
        </table>

        <div style='margin-top: 30px; float: right; width: 200px; text-align: center;'>
            <p>Indramayu, " . date('d-m-Y') . "</p>
            <p style='margin-bottom: 60px;'>Kepala Gudang,</p>
            <strong>( ________________ )</strong>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.stok-gudang');



Route::get('/cetak-rekap-rayon', function (Illuminate\Http\Request $request) {
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
                if (!$machine) continue;

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
        'namaBulan' => $namaBulan
    ]);
})->name('cetak.service-rayon');