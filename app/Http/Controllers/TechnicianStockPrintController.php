<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TechnicianStockHistory;
use App\Services\TechnicianStockReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TechnicianStockPrintController extends Controller
{
    public function __construct(
        private readonly TechnicianStockReportService $reportService,
    ) {}

    public function __invoke(Request $request): View
    {
        // Dropdown bulan mengirim "08"; aturan 'integer' menolak angka berawalan nol.
        $request->merge([
            'month' => (int) $request->input('month'),
            'year'  => (int) $request->input('year'),
        ]);

        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year'  => ['required', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) $validated['month'];
        $year  = (int) $validated['year'];

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $groupBy = fn(TechnicianStockHistory $history) => $history->technician_id ?? 'unknown';

        // Mutasi di dalam periode.
        $groupedHistories = TechnicianStockHistory::query()
            ->with(['technician', 'sparepart'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy($groupBy);

        // Baris terakhir per teknisi + part sampai akhir periode.
        $latestIds = TechnicianStockHistory::query()
            ->where('created_at', '<=', $end)
            ->selectRaw('MAX(id) as id')
            ->groupBy('technician_id', 'sparepart_id');

        // Part yang masih ada di tas tetapi tidak bergerak di periode ini.
        $carryOver = TechnicianStockHistory::query()
            ->with(['technician', 'sparepart'])
            ->whereIn('id', $latestIds)
            ->where('created_at', '<', $start)
            ->where('saldo_akhir', '>', 0)
            ->get()
            ->groupBy($groupBy);

        $now = Carbon::now();

        return view('exports.technician-stock-print', [
            'reports'     => $this->reportService->build($groupedHistories, $carryOver),
            'month'       => $month,
            'year'        => $year,
            'periodStart' => $start,
            // Bulan berjalan: posisi stok akhir adalah hari ini, bukan akhir bulan.
            'periodEnd'   => $end->isFuture() ? $now->copy()->startOfDay() : $end->copy()->startOfDay(),
            'printedAt'   => $now,
        ]);
    }
}
