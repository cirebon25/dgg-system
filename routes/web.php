<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceLog;
use Illuminate\Http\Request;

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