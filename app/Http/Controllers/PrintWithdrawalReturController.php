<?php

namespace App\Http\Controllers;

use App\Models\AccommodationClaim;
use App\Models\MachineReturn;
use App\Models\MachineWithdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrintWithdrawalReturController extends Controller
{
    // dipindah dari: Route::get('/cetak-surat-penarikan/{id}', ...)->name('cetak.surat-penarikan')
    public function suratPenarikan($id)
    {
        $withdrawal = MachineWithdrawal::with(['machine', 'customer'])->findOrFail($id);
        return view('print.surat-penarikan', compact('withdrawal'));
    }

    // dipindah dari: Route::get('/withdrawal/rekap', ...)->name('withdrawal.rekap')
    public function rekapWithdrawal(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year  = $request->query('year',  date('Y'));

        $records = MachineWithdrawal::with(['machine', 'customer'])
            ->whereMonth('tanggal_tarik', (int) $month)
            ->whereYear('tanggal_tarik',  (int) $year)
            ->orderBy('tanggal_tarik', 'asc')
            ->get();

        $namaBulan = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        return view('print.withdrawal-rekap', compact('records', 'namaBulan', 'month', 'year'));
    }

    // dipindah dari: Route::get('/cetak-surat-retur/{id}', ...)->name('cetak.surat-retur')
    // public function suratRetur($id)
    // {
    //     $retur = MachineReturn::with(['machine'])->findOrFail($id);
    //     return view('print.surat-retur', compact('retur'));
    // }

    public function suratRetur($id)
    {
        $query = MachineReturn::with('machine');

        // UUID = batch dari form retur, angka = id satu retur (link lama)
        if (Str::isUuid($id)) {
            $query->where('batch_id', $id);
        } else {
            $query->whereKey($id);
        }

        $returns = $query->orderBy('created_at')->get();

        abort_if($returns->isEmpty(), 404);

        $tanggal = Carbon::parse($returns->first()->tanggal_retur)->toDateString();

        return view('cetak.surat-retur-tanggal', compact('returns', 'tanggal'));
    }

    // dipindah dari: Route::get('/cetak-surat-retur-tanggal/{tanggal}', ...)->name('cetak.surat-retur-tanggal')
    public function suratReturTanggal($tanggal)
    {
        $returns = MachineReturn::with('machine')
            ->whereDate('tanggal_retur', $tanggal)
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        if ($returns->isEmpty()) abort(404);

        $tanggal = Carbon::parse($tanggal)->toDateString();

        return view('cetak.surat-retur-tanggal', compact('returns', 'tanggal'));
    }

    // dipindah dari: Route::get('/cetak-klaim-akomodasi/{id}', ...)->name('cetak.klaim-akomodasi')
    // public function klaimAkomodasi($id)
    // {
    //     $claim = AccommodationClaim::with([
    //         'technician',
    //         'visits',
    //     ])->findOrFail($id);

    //     return view('print.klaim-akomodasi', compact('claim'));
    // }

    public function klaimAkomodasi($id)
    {
        $claim = AccommodationClaim::with([
            'technician',
            'visits',
        ])->findOrFail($id);

        return view('print.klaim-akomodasi', compact('claim'));
    }
}
