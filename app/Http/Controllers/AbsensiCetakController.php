<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsensiCetakController extends Controller
{
    public function __invoke(Request $request)
    {
        $dari   = Carbon::parse($request->query('dari') ?: now()->startOfMonth())->startOfDay();
        $sampai = Carbon::parse($request->query('sampai') ?: now())->endOfDay();

        $names = AttendanceEmployee::pluck('name', 'pin');

        $rows = AttendanceLog::query()
            ->whereBetween('scan_at', [$dari, $sampai])
            ->orderBy('scan_at')
            ->get()
            ->groupBy(fn($log) => $log->scan_at->toDateString() . '|' . $log->pin)
            ->map(function ($group) use ($names) {
                $first = $group->first();
                $last  = $group->last();

                return [
                    'tanggal' => $first->scan_at->toDateString(),
                    'pin'     => $first->pin,
                    'nama'    => $names[$first->pin] ?? '-',
                    'masuk'   => $first->scan_at->format('H:i'),
                    'pulang'  => $group->count() > 1 ? $last->scan_at->format('H:i') : '-',
                    'jumlah'  => $group->count(),
                ];
            })
            ->sortBy([['tanggal', 'asc'], ['nama', 'asc']])
            ->values();

        return view('cetak.absensi', [
            'rows'   => $rows,
            'dari'   => $dari,
            'sampai' => $sampai,
        ]);
    }
}