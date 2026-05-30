<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\PartReplacement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PartReplacementController extends Controller
{
    // ===== CETAK PER MESIN =====
    public function cetakPerMesin(Request $request)
    {
        $machine = Machine::with('customer')->findOrFail($request->machine_id);

        // Ambil semua riwayat part mesin ini, group per sparepart
        $data = PartReplacement::with(['sparepart'])
            ->where('machine_id', $machine->id)
            ->orderBy('sparepart_id')
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->groupBy('sparepart_id');

        // Counter terakhir mesin ini
        $counterTerakhir = \App\Models\ServiceLog::where('machine_id', $machine->id)
            ->latest('id')
            ->value('counter_bw') ?? 0;

        $pdf = Pdf::loadView('cetak.part-per-mesin', compact('machine', 'data', 'counterTerakhir'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Riwayat-Part-' . $machine->serial_number . '.pdf');
    }

    // ===== CETAK PER BULAN =====
    public function cetakPerBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = PartReplacement::with(['machine.customer', 'sparepart'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('machine_id')
            ->orderBy('sparepart_id')
            ->orderBy('tanggal')
            ->get()
            ->groupBy('machine_id');

        $namaBulan = [
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

        $pdf = Pdf::loadView('cetak.part-per-bulan', compact('data', 'bulan', 'tahun', 'namaBulan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Riwayat-Part-' . $namaBulan[$bulan] . '-' . $tahun . '.pdf');
    }
}
