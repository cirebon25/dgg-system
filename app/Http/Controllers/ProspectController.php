<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Models\ProspectVisit;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function laporan(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $marketings = Marketing::orderBy('nama_marketing')->get();

        $dataPerMarketing = $marketings->map(function ($marketing) use ($month, $year) {
            $visits = ProspectVisit::with('prospect')
                ->where('marketing_id', $marketing->id)
                ->whereMonth('tanggal_kunjungan', $month)
                ->whereYear('tanggal_kunjungan',  $year)
                ->orderBy('tanggal_kunjungan')
                ->get();

            return [
                'marketing' => $marketing,
                'visits'    => $visits,
                'total'     => $visits->count(),
                'interest'  => $visits->where('hasil_kunjungan', 'Interest')->count(),
                'followup'  => $visits->where('hasil_kunjungan', 'Follow Up')->count(),
                'closing'   => $visits->where('hasil_kunjungan', 'Closing')->count(),
                'gagal'     => $visits->where('hasil_kunjungan', 'Gagal')->count(),
            ];
        })->filter(fn($d) => $d['total'] > 0)->values(); // hanya marketing yang punya visit bulan itu

        $bulanNama = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return view('reports.prospect-laporan', [
            'dataPerMarketing' => $dataPerMarketing,
            'bulan'            => $bulanNama[$month] ?? $month,
            'tahun'            => $year,
        ]);
    }
}
