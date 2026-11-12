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
        // 1. Normalisasi input: kosong / tidak valid -> bulan & tahun sekarang
        $bulan = (int) ($request->input('bulan') ?: now()->month);
        $tahun = (int) ($request->input('tahun') ?: now()->year);

        if ($bulan < 1 || $bulan > 12) {
            $bulan = now()->month;
        }
        if ($tahun < 2000) {
            $tahun = now()->year;
        }

        // 2. Query riwayat ganti part pada bulan & tahun tersebut
        $data = PartReplacement::with(['machine.customer', 'sparepart'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('machine_id')
            ->orderBy('sparepart_id')
            ->orderBy('tanggal')
            ->get()
            ->groupBy('machine_id');

        // 3. Nama bulan pakai kunci angka
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $pdf = Pdf::loadView('cetak.part-per-bulan', compact('data', 'bulan', 'tahun', 'namaBulan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Riwayat-Part-' . $namaBulan[$bulan] . '-' . $tahun . '.pdf');
    }
}
