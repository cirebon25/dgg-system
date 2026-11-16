<?php

namespace App\Http\Controllers;

use App\Models\Rayon;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class RekapMingguanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        // Minggu dari tanggal yang dipilih: Senin s/d Minggu
        $start = Carbon::parse($request->tanggal)->startOfWeek(Carbon::MONDAY);
        $end   = $start->copy()->endOfWeek(Carbon::SUNDAY);

        // 7 hari dalam minggu tersebut
        $days = collect(CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()))->values();

        $rayons = Rayon::orderBy('nama_rayon')->get();

        return view('cetak.rekap-mingguan', compact('rayons', 'start', 'end', 'days'));
    }
}
