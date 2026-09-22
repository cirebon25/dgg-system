<?php

use App\Http\Controllers\CashLedgerPrintController;
use App\Http\Controllers\CashMutationPrintController;
use App\Http\Controllers\CetakPemasanganController;
use App\Http\Controllers\CetakSwapController;
use App\Http\Controllers\LapPartBdgController;
use App\Http\Controllers\MachineReportController;
use App\Http\Controllers\MrcController;
use App\Http\Controllers\PartReplacementController;
use App\Http\Controllers\PoPartController;
use App\Http\Controllers\PrintMesinController;
use App\Http\Controllers\PrintRayonController;
use App\Http\Controllers\PrintRekapSparepartController;
use App\Http\Controllers\PrintServiceController;
use App\Http\Controllers\PrintSparepartTransController;
use App\Http\Controllers\PrintWithdrawalReturController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaldoSparepartController;
use App\Http\Controllers\SparepartOutflowController;
use App\Http\Controllers\DeploymentPrintController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TechnicianStockPrintController;
use App\Http\Controllers\InvoicePrintController;
use App\Models\Invoice;



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

/*
|--------------------------------------------------------------------------
| Print: Service Log (lihat App\Http\Controllers\PrintServiceController)
|--------------------------------------------------------------------------
*/
Route::get('/print-service-bulk', [PrintServiceController::class, 'bulk'])
    ->name('print.service.bulk')->middleware('auth');

Route::get('/service-log/{record}/print', [PrintServiceController::class, 'print'])
    ->name('service-log.print');

Route::get('/sparepart/monitor-umur/{machine_id}', [PrintServiceController::class, 'monitorUmur'])
    ->name('sparepart.monitor');

Route::get('/admin/service-log/{serviceLog}/surat-jalan', [PrintServiceController::class, 'suratJalan'])
    ->name('service-log.surat-jalan')->middleware(['auth']);

Route::get('/cetak-stok-gudang/excel', [PrintMesinController::class, 'stokGudangExcel'])
    ->name('cetak.stok-gudang.excel');
/*
|--------------------------------------------------------------------------
| Print: Mesin — QR, histori, alokasi, stok gudang, SJ baru
| (lihat App\Http\Controllers\PrintMesinController)
|--------------------------------------------------------------------------
*/
Route::get('/cetak-alokasi-customer', [PrintMesinController::class, 'alokasiCustomer'])
    ->name('cetak.alokasi-customer');

Route::get('/mesin/{id}/cetak-qr', [PrintMesinController::class, 'cetakQr'])
    ->name('mesin.cetak-qr');

Route::get('/mesin/{id}/histori', [PrintMesinController::class, 'histori'])
    ->name('mesin.histori');

Route::get('/cetak-stok-gudang', [PrintMesinController::class, 'stokGudang'])
    ->name('cetak.stok-gudang');

Route::get('/cetak-alokasi-mesin', [PrintMesinController::class, 'alokasiMesin'])
    ->name('cetak.alokasi');

Route::get('/cetak-surat-jalan/{id}', [PrintMesinController::class, 'suratJalan'])
    ->name('cetak.surat-jalan');

Route::get('/cetak-stok-gudang/pdf', [PrintMesinController::class, 'stokGudangPdf'])
    ->name('cetak.stok-gudang.pdf');

/*
|--------------------------------------------------------------------------
| Print: Rekap Rayon, Service Rayon, Top Usage, Kinerja Teknisi/Rayon
| (lihat App\Http\Controllers\PrintRayonController)
|--------------------------------------------------------------------------
*/
Route::get('/cetak-rekap-rayon', [PrintRayonController::class, 'rekapRayon'])
    ->name('cetak.rekap-rayon');

Route::get('/cetak-service-rayon', [PrintRayonController::class, 'serviceRayon'])
    ->name('cetak.service-rayon');

Route::get('/cetak-top-usage', [PrintRayonController::class, 'topUsage'])
    ->name('cetak.top-usage');

Route::get('/print/technician-performance', [PrintRayonController::class, 'technicianPerformance'])
    ->name('print.tech-performance');

Route::get('/print/performance-rayon', [PrintRayonController::class, 'performanceRayon'])
    ->name('print.performance-rayon');

/*
|--------------------------------------------------------------------------
| Print: Bukti Pinjam, Kartu Stok Teknisi, SJ Rolling
| (lihat App\Http\Controllers\PrintSparepartTransController)
|--------------------------------------------------------------------------
*/
Route::get('/cetak-bukti-pinjam/{id}', [PrintSparepartTransController::class, 'buktiPinjam'])
    ->name('cetak.bukti-pinjam');

Route::get('/cetak-sj-rolling/{replacementId}', [PrintSparepartTransController::class, 'sjRolling'])
    ->name('cetak.sj-rolling');

Route::get('/cetak-bukti-pinjam-multi/{id}', [PrintSparepartTransController::class, 'buktiPinjamMulti'])
    ->name('cetak.bukti-pinjam-multi');

Route::get('/cetak-kartu-stok/{technician_id}', [PrintSparepartTransController::class, 'kartuStok'])
    ->name('cetak.kartu-stok');

Route::get('/cetak-kartu-stok-semua', [PrintSparepartTransController::class, 'kartuStokSemua'])
    ->name('cetak.kartu-stok-semua');

/*
|--------------------------------------------------------------------------
| Print: Penarikan & Retur Mesin
| (lihat App\Http\Controllers\PrintWithdrawalReturController)
|--------------------------------------------------------------------------
*/
Route::get('/cetak-surat-penarikan/{id}', [PrintWithdrawalReturController::class, 'suratPenarikan'])
    ->name('cetak.surat-penarikan');

Route::get('/withdrawal/rekap', [PrintWithdrawalReturController::class, 'rekapWithdrawal'])
    ->name('withdrawal.rekap')->middleware('auth');

Route::get('/cetak-surat-retur/{id}', [PrintWithdrawalReturController::class, 'suratRetur'])
    ->name('cetak.surat-retur')->middleware('auth');

Route::get('/cetak-surat-retur-tanggal/{tanggal}', [PrintWithdrawalReturController::class, 'suratReturTanggal'])
    ->name('cetak.surat-retur-tanggal');

Route::get('/cetak-klaim-akomodasi/{id}', [PrintWithdrawalReturController::class, 'klaimAkomodasi'])
    ->name('cetak.klaim-akomodasi')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Print: Rekap Pengeluaran Sparepart (gabungan service log + deployment)
| (lihat App\Http\Controllers\PrintRekapSparepartController)
|--------------------------------------------------------------------------
*/
Route::get('/cetak-rekap-sparepart', [PrintRekapSparepartController::class, 'index'])
    ->name('cetak.rekap-sparepart');

/*
|--------------------------------------------------------------------------
| Controller yang sudah ada sebelumnya — tidak diubah
|--------------------------------------------------------------------------
*/
Route::get('/admin/rekap-horizontal', [ReportController::class, 'rekapHorizontal'])
    ->name('rekap.horizontal')->middleware(['auth']);

Route::get('/cetak-pemasangan-baru/{bulan?}/{tahun?}', [CetakPemasanganController::class, 'index'])
    ->name('cetak.pemasangan');

Route::get('/cetak/part-per-mesin', [PartReplacementController::class, 'cetakPerMesin'])
    ->name('cetak.part.mesin');

Route::get('/cetak/part-per-bulan', [PartReplacementController::class, 'cetakPerBulan'])
    ->name('cetak.part.bulan');

Route::get('/cetak-tukar-guling', [CetakSwapController::class, 'index'])
    ->name('cetak.swap');

Route::get('/saldo-sparepart', [SaldoSparepartController::class, 'index'])
    ->name('saldo-sparepart');

Route::get('/sparepart/report/outflow', [SparepartOutflowController::class, 'index'])
    ->name('sparepart.report.outflow');

// Route::get('/cash-mutation/print/{id}', [CashMutationPrintController::class, 'print'])
//     ->name('cash-mutation.print')->middleware(['auth']);

Route::get('/cash-mutation/print/{cashMutation}', [CashMutationPrintController::class, 'print'])
    ->name('cash-mutation.print')
    ->middleware(['auth']);

Route::get('/cetak-kas-bulanan', [CashLedgerPrintController::class, 'cetakBulanan'])
    ->name('cetak.kas-bulanan')->middleware('auth');

Route::get('/report/rekap-mesin', [MachineReportController::class, 'rekapUnitCustomer'])
    ->name('report.rekap-mesin');

Route::get('/lap-part-bdg', [LapPartBdgController::class, 'index'])
    ->name('lap-part-bdg');

Route::get('/po-part/{id}/print', [PoPartController::class, 'print'])
    ->name('po-part.print');

Route::get('/mrc/rekap', [MrcController::class, 'rekap'])
    ->name('mrc.rekap');

Route::get('/mrc/tagihan', [MrcController::class, 'tagihan'])
    ->name('mrc.tagihan');

Route::get('/prospect/laporan', [ProspectController::class, 'laporan'])
    ->name('prospect.laporan');

Route::get('/sparepart/pemakaian-bulanan', [PrintRekapSparepartController::class, 'pemakaianBulanan'])
    ->name('sparepart.pemakaian-bulanan');

Route::get('/sparepart/pemakaian-matrix', [PrintRekapSparepartController::class, 'pemakaianMatrix'])
    ->name('sparepart.pemakaian-matrix');



Route::get('/cetak-surat-retur/{id}', function ($id) {
    $retur = \App\Models\MachineReturn::with(['machine'])->findOrFail($id);
    return view('print.surat-retur', compact('retur'));
})->name('cetak.surat-retur')->middleware('auth');

Route::get('/cetak/part-per-mesin/{machine_id}', [PrintServiceController::class, 'partPerMesin'])
    ->name('cetak.part-per-mesin');

Route::get('/mesin/{id}/tracking', [PrintMesinController::class, 'trackingMesin'])
    ->name('mesin.tracking');

Route::get('/cetak-kartu-stok-sparepart/{sparepart_id}', [PrintSparepartTransController::class, 'kartuStokSparepart'])
    ->name('cetak.kartu-stok-sparepart');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/deployments/print-yearly', [\App\Http\Controllers\DeploymentPrintController::class, 'printYearly'])
        ->name('deployments.print-yearly');
});

Route::get('/deployments/{deployment}/print', [DeploymentPrintController::class, 'printServiceCard'])
    ->name('deployment.print');

fn() => null; // placeholder agar tidak error jika butuh namespace, gunakan kode di bawah:

Route::get('/custom/confirm-password', function () {
    return view('auth.custom-confirm-password');
})->name('password.confirm')->middleware(['auth']);

// Route::post('/custom/confirm-password', function (Request $request) {
//     $request->validate(['password' => ['required']]);

//     if (! Hash::check($request->password, $request->user()->password)) {
//         return back()->withErrors(['password' => __('auth.password')]);
//     }

//     $request->session()->passwordConfirm();

//     return redirect()->intended();
// })->middleware(['auth']);


Route::post('/custom/confirm-password', function (Request $request) {
    $request->validate(['password' => ['required']]);

    // Ganti 'pin_rahasia_anda' dengan password/PIN khusus yang Anda inginkan
    $pinKhusus = '123456'; // Contoh PIN khusus modul pinjam part

    if ($request->password !== $pinKhusus) {
        return back()->withErrors(['password' => 'Password atau PIN modul salah!']);
    }

    // Tandai sesi bahwa modul ini sudah dikonfirmasi (berlaku 30 menit)
    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended();
})->middleware(['auth']);

Route::post('/custom/confirm-password', function (Request $request) {
    $request->validate(['password' => ['required']]);

    // Ambil PIN dari database (jika belum diset, default ke '123456')
    $pinAktif = DB::table('settings')->where('key', 'modul_pinjam_pin')->value('value') ?? '123456';

    if ($request->password !== $pinAktif) {
        return back()->withErrors(['password' => 'PIN modul salah!']);
    }

    // Tandai sesi bahwa modul ini sudah dikonfirmasi (berlaku 1x transaksi)
    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended();
})->middleware(['auth']);


Route::get('/admin/technician-stock-histories/print', [TechnicianStockPrintController::class, '__invoke'])
    ->name('technician-stock.print')
    ->middleware(['auth']); // sesuaikan middleware auth filament Anda




// Route Cetak Satuan (Per Baris Invoice)
Route::get('/admin/invoices/{record}/print', function (Invoice $record) {
    return view('invoices.print', ['invoice' => $record]);
})->name('invoice.print')->middleware(['web', 'auth']);

// Route Cetak Rekap Semua Data
Route::get('/admin/invoices/print-all', function (Request $request) {
    $invoices = Invoice::with('customer')->orderBy('tanggal', 'desc')->get();
    return view('invoices.print-all', ['invoices' => $invoices]);
})->name('invoice.print-all')->middleware(['web', 'auth']);
