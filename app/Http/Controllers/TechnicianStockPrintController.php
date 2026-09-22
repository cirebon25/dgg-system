<?php

namespace App\Http\Controllers;

use App\Models\TechnicianStockHistory;
use Illuminate\Http\Request;

class TechnicianStockPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        // Ambil data berdasarkan bulan & tahun, lalu group berdasarkan technician_id
        $histories = TechnicianStockHistory::with(['technician', 'sparepart'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($item) {
                // Kelompokkan berdasarkan ID teknisi (jika null, masukkan ke group 'Tidak Diketahui')
                return $item->technician_id ?? 'unknown';
            });

        return view('exports.technician-stock-print', [
            'groupedHistories' => $histories,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
