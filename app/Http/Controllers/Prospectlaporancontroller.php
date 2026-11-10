<?php

namespace App\Http\Controllers;

use App\Models\ProspectVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProspectLaporanController extends Controller
{
    /** Cetak Laporan Sales: ringkasan eksekutif untuk keputusan */
    public function ringkasan(Request $request)
    {
        return $this->tampilkan($request, 'cetak.laporan-sales-keputusan');
    }

    /** Rincian Laporan Sales: daftar kunjungan lengkap per marketing */
    public function rincian(Request $request)
    {
        return $this->tampilkan($request, 'cetak.Prospect-laporan');
    }

    private function tampilkan(Request $request, string $view)
    {
        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year'  => ['required', 'integer', 'min:2024'],
        ]);

        $bulan = Carbon::create((int) $data['year'], (int) $data['month'], 1)
            ->locale('id')
            ->translatedFormat('F');

        return view($view, [
            'bulan'            => $bulan,
            'tahun'            => $data['year'],
            'dataPerMarketing' => $this->dataLaporan($data['month'], $data['year']),
        ]);
    }

    private function dataLaporan($month, $year): Collection
    {
        return ProspectVisit::with(['prospect', 'marketing'])
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->orderBy('tanggal_kunjungan')
            ->get()
            ->groupBy('marketing_id')
            ->map(fn($visits) => [
                'marketing' => $visits->first()->marketing,
                'visits'    => $visits,
                'total'     => $visits->count(),
                'interest'  => $visits->where('hasil_kunjungan', 'Interest')->count(),
                'followup'  => $visits->where('hasil_kunjungan', 'Follow Up')->count(),
                'closing'   => $visits->where('hasil_kunjungan', 'Closing')->count(),
                'gagal'     => $visits->where('hasil_kunjungan', 'Gagal')->count(),
            ])
            ->filter(fn($d) => $d['marketing'])
            ->values();
    }
}
