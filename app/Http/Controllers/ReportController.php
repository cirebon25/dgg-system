<?php

namespace App\Http\Controllers;

use App\Models\Rayon;
use App\Models\ServiceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function rekapHorizontal(Request $request)
    {
        // Support dua nama parameter: month/year (baru) dan bulan/tahun (lama dari pusat cetak)
        $month = $request->input('month') ?? $request->input('bulan') ?? now()->month;
        $year  = $request->input('year')  ?? $request->input('tahun')  ?? now()->year;

        $rayons = Rayon::with(['customers.machines' => function ($q) use ($month, $year) {
            $q->with(['serviceLogs' => function ($logQ) use ($month, $year) {
                $logQ->whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->with(['technician', 'serviceLogSpareparts.sparepart'])
                    ->orderBy('tanggal', 'asc');
            }]);
        }])->get();

        return view('reports.rekap-horizontal', compact('rayons', 'month', 'year'));
    }
}
