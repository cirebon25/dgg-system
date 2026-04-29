<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceLog;
use Illuminate\Http\Request;
use App\Models\ServiceLogSparepart;
use Illuminate\Support\Facades\DB;
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



Route::get('/service-log/report/monthly', function (Request $request) {
    $month = $request->query('month');
    $year = $request->query('year');

    $logs = ServiceLog::whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->with(['machine.customer', 'technician'])
        ->get();

    return view('print.monthly-report', [
        'logs' => $logs,
        'month' => $month,
        'year' => $year
    ]);
})->name('service-log.monthly');

// ROUTE CETAK BULANAN 
Route::get('/service-log/report/monthly', function (Request $request) {
    $month = $request->query('month');
    $year = $request->query('year');

    // Ambil data servis berdasarkan bulan dan tahun
    $logs = ServiceLog::whereYear('tanggal', $year)
        ->whereMonth('tanggal', $month)
        ->with(['machine.customer', 'technician'])
        ->get();

    return view('print.monthly-report', [
        'logs' => $logs,
        'month' => $month,
        'year' => $year
    ]);
})->name('service-log.monthly');

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
    // Ambil data (Logika yang tadi kita buat)
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

    // Tampilan HTML-nya
    $html = "
    <html>
    <head>
        <title>Laporan Alokasi Mesin DGG</title>
        <style>
            body { font-family: sans-serif; font-size: 12px; padding: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            .bg-rayon { background-color: #1e40af; color: white; font-weight: bold; }
            .bg-kota { background-color: #e5e7eb; font-weight: bold; }
            .bg-total-kota { background-color: #fef9c3; font-weight: bold; }
            .bg-total-rayon { background-color: #dcfce7; font-weight: bold; font-size: 14px; }
            .text-right { text-align: right; }
            header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            
            /* -- INI KODE TAMBAHAN AGAR WARNA TEMBUS SAAT PRINT -- */
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
            <h1 style='margin:0;'>DGG SYSTEM - OPERATIONAL HUB</h1>
            <h2 style='margin:5px 0;'>LAPORAN ALOKASI UNIT MESIN</h2>
            <p>Dicetak pada: " . date('d-m-Y H:i') . "</p>
        </header>
        <table>
            <thead>
                <tr style='background: #333; color: #fff;'>
                    <th>RAYON / KOTA / TIPE MESIN</th>
                    <th width='150' class='text-right'>JUMLAH UNIT</th>
                </tr>
            </thead>
            <tbody>";

    $grandTotal = 0;
    foreach ($data as $namaRayon => $kotas) {
        $html .= "<tr class='bg-rayon'><td colspan='2'>RAYON: $namaRayon</td></tr>";
        $totalRayon = 0;
        foreach ($kotas as $namaKota => $types) {
            $html .= "<tr class='bg-kota'><td colspan='2'>&nbsp;&nbsp;📍 Kota/Kab: $namaKota</td></tr>";
            $totalKota = 0;
            foreach ($types as $item) {
                $html .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - {$item->tipe_model}</td><td class='text-right'>{$item->qty} Unit</td></tr>";
                $totalKota += $item->qty;
            }
            $html .= "<tr class='bg-total-kota'><td class='text-right'>Total Unit di $namaKota:</td><td class='text-right'>$totalKota Unit</td></tr>";
            $totalRayon += $totalKota;
        }
        $html .= "<tr class='bg-total-rayon'><td class='text-right'>TOTAL AKUMULASI RAYON $namaRayon:</td><td class='text-right'>$totalRayon Unit</td></tr>";
        $grandTotal += $totalRayon;
    }

    $html .= "
            </tbody>
            <tfoot>
                <tr style='background: #000; color: #fff; font-size: 16px;'>
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
    // 1. AMBIL DATA DARI DATABASE
    $data = DB::table('deployments')
        ->join('machines', 'deployments.machine_id', '=', 'machines.id')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
        ->whereMonth('deployments.created_at', $bulan)
        ->whereYear('deployments.created_at', $tahun)
        ->select(
            'deployments.created_at as tgl_pasang',
            'machines.serial_number',
            'machines.tipe_model', // Sudah pakai tipe_model sesuai database Boss
            'customers.nama_customer',
            'customers.kota',
            'rayons.nama_rayon'
        )
        ->orderBy('deployments.created_at', 'asc')
        ->get();

    // Konversi angka bulan ke nama bulan Indonesia
    $bulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $namaBulan = $bulanIndo[$bulan] ?? 'Tidak Diketahui';

    // 2. STRUKTUR HTML FULL (LANDSCAPE)
    $html = "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Laporan Pemasangan Baru - DGG System</title>
        <style>
            /* SETINGAN KERTAS LANDSCAPE */
            @page { 
                size: landscape; 
                margin: 15mm; 
            }
            
            body { 
                font-family: 'Helvetica', 'Arial', sans-serif; 
                font-size: 12px; 
                color: #333;
                line-height: 1.5;
            }

            .header { 
                text-align: center; 
                border-bottom: 3px double #000; 
                padding-bottom: 10px; 
                margin-bottom: 20px;
            }

            .header h1 { margin: 0; font-size: 22px; color: #1e40af; }
            .header h2 { margin: 5px 0; font-size: 18px; }
            .header p { margin: 0; color: #666; }

            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 10px;
            }

            th { 
                background-color: #1e40af; 
                color: white; 
                text-transform: uppercase;
                padding: 12px 8px;
                border: 1px solid #000;
            }

            td { 
                padding: 10px 8px; 
                border: 1px solid #666;
                vertical-align: middle;
            }

            tr:nth-child(even) { background-color: #f8fafc; }

            .text-center { text-align: center; }
            .font-bold { font-weight: bold; }
            
            .footer { 
                margin-top: 40px; 
                width: 100%;
            }

            .ttd-box { 
                float: right; 
                width: 250px; 
                text-align: center;
            }
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
                    <th width='50'>No</th>
                    <th width='100'>Tgl Pasang</th>
                    <th width='150'>SN Mesin</th>
                    <th width='150'>Tipe Model</th>
                    <th>Nama Customer</th>
                    <th>Wilayah / Rayon</th>
                </tr>
            </thead>
            <tbody>";

    // LOOPING DATA KE TABEL
    if ($data->isEmpty()) {
        $html .= "<tr><td colspan='6' class='text-center'>Tidak ada data pemasangan baru pada periode ini.</td></tr>";
    } else {
        foreach ($data as $index => $row) {
            $no = $index + 1;
            $tgl = date('d-m-Y', strtotime($row->tgl_pasang));
            $html .= "
            <tr>
                <td class='text-center'>$no</td>
                <td class='text-center'>$tgl</td>
                <td class='font-bold'>$row->serial_number</td>
                <td>$row->tipe_model</td>
                <td>$row->nama_customer</td>
                <td>$row->kota ($row->nama_rayon)</td>
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
                <p>Rudianto</p>
            </div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.pemasangan');

Route::get('/cetak-surat-jalan/{id}', function ($id) {
    $data = DB::table('deployments')
        ->join('machines', 'deployments.machine_id', '=', 'machines.id')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->where('deployments.id', $id)
        ->select(
            'deployments.created_at as tgl_kirim',
            'machines.serial_number',
            'machines.tipe_model',
            'customers.nama_customer',
            'customers.alamat', // Pastikan kolom 'alamat' ada di tabel customers
            'customers.kota'
        )
        ->first();

    if (!$data) return "Data tidak ditemukan.";

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Surat Jalan - $data->serial_number</title>
        <style>
            @page { size: landscape; margin: 10mm; }
            body { font-family: sans-serif; font-size: 12px; border: 2px solid #000; padding: 20px; height: 100%; }
            .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
            .content { display: flex; justify-content: space-between; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #000; padding: 12px; text-align: left; }
            .footer-ttd { margin-top: 50px; display: flex; justify-content: space-around; text-align: center; }
            .ttd-box { width: 200px; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header'>
            <h1 style='margin:0;'>DGG SYSTEM - SURAT JALAN / DELIVERY ORDER</h1>
            <p>Nomor: SJ/DGG/" . date('Ymd', strtotime($data->tgl_kirim)) . "/$id</p>
        </div>
        
        <div class='content'>
            <div style='width: 50%;'>
                <strong>Penerima:</strong><br>
                $data->nama_customer<br>
                $data->alamat<br>
                $data->kota
            </div>
            <div style='width: 50%; text-align: right;'>
                <strong>Tanggal Pengiriman:</strong> " . date('d-m-Y', strtotime($data->tgl_kirim)) . "
            </div>
        </div>

        <table>
            <thead>
                <tr style='background: #eee;'>
                    <th>No</th>
                    <th>Nama Barang / Deskripsi</th>
                    <th>Serial Number</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Mesin Fotokopi Canon $data->tipe_model</td>
                    <td><strong>$data->serial_number</strong></td>
                    <td>1 Unit</td>
                </tr>
            </tbody>
        </table>

        <div style='margin-top: 20px;'>
            <strong>Keterangan:</strong> Barang telah diterima dalam kondisi baik dan berfungsi normal.
        </div>

        <div class='footer-ttd'>
            <div class='ttd-box'>
                Penerima,<br><br><br><br>
                ( ________________ )
            </div>
            <div class='ttd-box'>
                Teknisi,<br><br><br><br>
                ( ________________ )
            </div>
            <div class='ttd-box'>
                Pengirim,<br><br><br><br>
                ( ________________ )
            </div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.sj');

Route::get('/cetak-rekap-service/{bulan}/{tahun}', function ($bulan, $tahun) {
    $data = DB::table('service_logs')
        ->join('machines', 'service_logs.machine_id', '=', 'machines.id')
        ->join('customers', 'service_logs.customer_id', '=', 'customers.id')
        ->join('technicians', 'service_logs.technician_id', '=', 'technicians.id')
        ->whereMonth('service_logs.tanggal', $bulan)
        ->whereYear('service_logs.tanggal', $tahun)
        ->select('service_logs.*', 'machines.serial_number', 'machines.tipe_model', 'customers.nama_customer', 'technicians.nama_technician')
        ->get();

    $html = "
    <html>
    <head>
        <title>Rekap Service Log</title>
        <style>
            @page { size: landscape; margin: 10mm; }
            body { font-family: sans-serif; font-size: 11px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f3f4f6; }
            .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        </style>
    </head>
    <body onload='window.print()'>
        <div class='header'>
            <h1>DGG SYSTEM - REKAP SERVICE LOG</h1>
            <p>Periode: $bulan / $tahun</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tgl</th>
                    <th>SN Mesin</th>
                    <th>Customer</th>
                    <th>Kerusakan / Perbaikan</th>
                    <th>Teknisi</th>
                </tr>
            </thead>
            <tbody>";
    foreach ($data as $row) {
        $html .= "<tr>
            <td>$row->tanggal</td>
            <td>$row->serial_number ($row->tipe_model)</td>
            <td>$row->nama_customer</td>
            <td>$row->kerusakan / $row->perbaikan</td>
            <td>$row->nama_technician</td>
        </tr>";
    }Route::get('/cetak-rekap-sparepart/{bulan}/{tahun}', function ($bulan, $tahun) {
    $data = DB::table('service_log_spareparts')
        ->join('spareparts', 'service_log_spareparts.sparepart_id', '=', 'spareparts.id')
        ->whereMonth('service_log_spareparts.created_at', $bulan)
        ->whereYear('service_log_spareparts.created_at', $tahun)
        ->select('spareparts.nama_sparepart', DB::raw('SUM(jumlah) as total_keluar'))
        ->groupBy('spareparts.nama_sparepart')
        ->get();

    $html = "
    <html>
    <head>
        <title>Rekap Pengeluaran Sparepart</title>
        <style>
            @page { size: portrait; margin: 15mm; }
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 10px; text-align: left; }
            th { background-color: #ef4444; color: white; }
        </style>
    </head>
    <body onload='window.print()'>
        <h1 style='text-align:center;'>DGG SYSTEM - REKAP PENGELUARAN SPAREPART</h1>
        <p style='text-align:center;'>Periode: $bulan / $tahun</p>
        <table>
            <thead>
                <tr>
                    <th>Nama Sparepart</th>
                    <th style='text-align:right;'>Total Keluar (Unit)</th>
                </tr>
            </thead>
            <tbody>";
    foreach ($data as $row) {
        $html .= "<tr>
            <td>$row->nama_sparepart</td>
            <td style='text-align:right;'>$row->total_keluar</td>
        </tr>";
    }
    $html .= "</tbody></table></body></html>";
    return response($html);
})->name('cetak.rekap-sparepart');
    $html .= "</tbody></table></body></html>";
    return response($html);
})->name('cetak.service-log-bulanan');

