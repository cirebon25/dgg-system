<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceLog;
use Illuminate\Http\Request;
use App\Models\ServiceLogSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\Machine;
use App\Models\Deployment;
use App\Models\Sparepart;

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
            .bg-rayon { background-color: #a9a9a9; color: black; font-weight: bold; }
            .bg-kota { background-color: #17a2b8; font-weight: bold; }
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
                <tr style='background: #a9a9a9; color: #fff;'>
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
                <tr style='background: #a9a9a9; color: #fff; font-size: 16px;'>
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

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>SJ - {$d->machine->serial_number}</title>
        <style>
            @page { size: landscape; margin: 10mm; }
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
            <br> {$d->customer->alamat} | {$d->customer->kota} <br/>
        </p>

        <table>
            <thead>
                <tr>
                    <th width='30'>NO</th>
                    <th>NAMA BARANG / DESKRIPSI</th>
                    <th width='100'>SN / KODE PART</th>
                    <th width='80'>JUMLAH</th>
                    <th width='60'>KETERANGAN/COUNTER</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><b>$merk {$d->machine->tipe_model}</b></td>
                    <td>{$d->machine->serial_number}</td>
                    <td>1 Unit</td>
                    <td> </td>
                </tr>";

    // --- BAGIAN INI YANG MENAMPILKAN SPAREPART ---
    $no = 2;
    foreach ($d->spareparts as $part) {
        $html .= "<tr>
            <td>$no</td>
            <td>{$part->nama_sparepart}</td>
            <td>" . ($part->code_part ?: $part->no_part ?: '-') . "</td>
            <td>{$part->pivot->jumlah} Pcs</td>
            <td> </td>
        </tr>";
        $no++;
    }

    $html .= "
            </tbody>
        </table>

        <div style='display: flex; justify-content: space-around; margin-top: 40px; text-align: center;'>
            <div>Admin,<br><br><br>( ____________ )</div>
            <div>Disetujui,<br><br><br>( ____________ )</div>
            <div>Teknisi,<br><br><br>( ____________ )</div>
            <div>Penerima,<br><br><br>( ____________ )</div>
        </div>
    </body>
    </html>";

    return response($html);
})->name('cetak.surat-jalan');

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


Route::get('/cetak-stok-gudang', function () {
    // 1. Ambil mesin di gudang (Status bukan Rented)
    $machines = \App\Models\Machine::where('status', '!=', 'Rented')->get();

    // 2. Kelompokkan agar hitungan per tipe/voltase akurat
    $groupedMachines = $machines->groupBy(function ($item) {
        return $item->tipe_model . '|' . $item->status . '|' . ($item->volt ?? '-');
    });

    $html = "
    <html>
    <head>
        <title></title> <!-- Judul tab dikosongkan agar tidak muncul saat diprint -->
        <style>
            body { font-family: sans-serif; font-size: 11px; padding: 10px; }
            .header-laporan { text-align: center; margin-bottom: 20px; }
            .header-laporan p { margin: 2px 0; font-weight: bold; }
            .judul-utama { font-size: 17px; text-decoration: underline; margin-top: 10px; font-weight: bold; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #000; padding: 6px; text-align: left; }
            th { background: #f2f2f2; text-align: center; text-transform: uppercase; font-size: 10px; }
            .text-center { text-align: center; }
            @media print { 
                @page { margin: 0.5cm; } /* Memperkecil margin kertas */
                * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            }
        </style>
    </head>
    <body onload='window.print()'>
        
        <div class='header-laporan'>
            <p>PT. DINAMIKA GLOBAL GEMILANG</p>
            <p>DEPO CIREBON</p>
            <div class='judul-utama'>LAPORAN STOK MESIN GUDANG</div>
        </div>

        <p>Tanggal Cetak: " . date('d-m-Y') . "</p>
        
        <table>
            <thead>
                <tr>
                    <th width='30'>NO</th>
                    <th width='50'>TYPE MESIN</th>
                    <th width='50'>VOLT</th>
                    <th width='50'>QTY</th>
                    <th width='50'>FINISHER</th>
                    <th width='50'>COVER</th>
                    <th width='50'>KASET</th>
                    <th width='50'>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>";
            
    $no = 1;
    $totalSemua = 0;

    foreach ($groupedMachines as $key => $group) {
        $first = $group->first();
        $qty = $group->count();
        $totalSemua += $qty;

        // Ambil keterangan unik (Tanpa Nomor Seri)
        $keteranganGabungan = $group->pluck('keterangan_awal')
            ->filter(fn($ket) => !empty(trim($ket ?? '')) && $ket !== '-')
            ->unique()
            ->implode(', ');

        // Hitung stok part per grup
        $fin = $group->filter(fn($m) => !empty(trim($m->finisher ?? '')) && trim($m->finisher) !== '-')->count();
        $cov = $group->filter(fn($m) => !empty(trim($m->cover ?? '')) && trim($m->cover) !== '-')->count();
        $kas = $group->filter(fn($m) => !empty(trim($m->kaset ?? '')) && trim($m->kaset) !== '-')->count();

        $html .= "<tr>
            <td class='text-center'>$no</td> 
            <td><b style='font-size:12px;'>{$first->tipe_model}</b></td>
            <td class='text-center'>{$first->volt}</td>
            <td class='text-center'><b style='font-size:12px;'>{$qty} Unit</b></td>
            <td class='text-center'>" . ($fin > 0 ? "{$fin}" : "-") . "</td>
            <td class='text-center'>" . ($cov > 0 ? "{$cov}" : "-") . "</td>
            <td class='text-center'>" . ($kas > 0 ? "{$kas}" : "-") . "</td>
            <td style='font-size: 10px; color: #333;'>" . ($keteranganGabungan ?: '-') . "</td>
        </tr>";
        $no++;
    }

    $html .= "
            <tr style='background: #f2f2f2; font-weight: bold;'>
                <td colspan='3' class='text-center'>TOTAL KESELURUHAN STOK</td>
                <td class='text-center' style='font-size:13px; background: #ddd;'>{$totalSemua} Unit</td>
                <td colspan='4'></td>
            </tr>
            </tbody>
        </table>
        
        <div style='margin-top: 30px; float: right; text-align: center; width: 200px;'>
            <p>Cirebon, " . date('d-m-Y') . "</p>
            <br><br><br>
            <p><b>( _________________ )</b></p>
            <p>Admin Gudang</p>
        </div>

    </body>
    </html>";

    return response($html);
})->name('cetak.stok-gudang');

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




