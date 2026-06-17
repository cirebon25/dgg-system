<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

class LapPartBdgController extends Controller
{
   public function index(Request $request)
{
    $month = $request->get('month', date('m'));
    $year  = $request->get('year', date('Y'));

    $spareparts = Sparepart::orderByRaw("
    CASE 
        WHEN no_part LIKE 'S%' THEN 0
        WHEN no_part LIKE 'P%' THEN 1
        ELSE 2
    END,
    CAST(SUBSTRING(no_part, 2) AS UNSIGNED)
")
->withSum('technicianStocks as saldo_teknisi', 'jumlah')
->get();

    $bulanNama = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
    ];

    return view('reports.lap-part-bdg', [
        'spareparts' => $spareparts,
        'bulan'      => $bulanNama[$month] ?? $month,
        'tahun'      => $year,
    ]);
}
}