<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use Illuminate\Http\Request;

class PrintServiceController extends Controller
{
    // dipindah dari: Route::get('/print-service-bulk', ...)->name('print.service.bulk')
    public function bulk(Request $request)
    {
        $ids = explode(',', $request->ids);
        $records = ServiceLog::with(['machine', 'technician', 'sparepart'])
            ->whereIn('id', $ids)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('print-service-bulk', compact('records'));
    }

    // dipindah dari: Route::get('/service-log/{record}/print', ...)->name('service-log.print')
    public function print(ServiceLog $record)
    {
        return view('print.service-log', ['record' => $record]);
    }

    // dipindah dari: Route::get('/sparepart/monitor-umur/{machine_id}', ...)->name('sparepart.monitor')
    public function monitorUmur($machine_id)
    {
        $machine = Machine::with(['deployment.customer', 'serviceLogs'])->findOrFail($machine_id);

        $partHistories = ServiceLogSparepart::whereHas('serviceLog', function ($q) use ($machine_id) {
            $q->where('machine_id', $machine_id);
        })->with(['sparepart', 'serviceLog'])->get();

        $latestLog = $machine->serviceLogs()->latest('tanggal')->first();
        $counterSekarangBW = $latestLog->counter_bw ?? 0;
        $counterSekarangCL = $latestLog->counter_color ?? 0;

        return view('print.sparepart-monitoring', compact('machine', 'partHistories', 'counterSekarangBW', 'counterSekarangCL'));
    }

    // dipindah dari: Route::get('/admin/service-log/{serviceLog}/surat-jalan', ...)->name('service-log.surat-jalan')
    public function suratJalan(ServiceLog $serviceLog)
    {
        return view('reports.surat-jalan', ['log' => $serviceLog]);
    }

    // Route::get('/cetak/part-per-mesin/{machine_id}', ...)->name('cetak.part-per-mesin')
    public function partPerMesin($machine_id)
    {
        $machine = Machine::with(['customer'])->findOrFail($machine_id);

        // Ambil semua riwayat ganti part untuk mesin ini
        // dikelompokkan per sparepart
        $data = \App\Models\PartReplacement::where('machine_id', $machine_id)
            ->with(['sparepart'])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('sparepart_id');

        // Counter terakhir dari service log terbaru
        $latestLog = ServiceLog::where('machine_id', $machine_id)
            ->latest('tanggal')
            ->first();

        $counterTerakhir = $latestLog->counter_bw ?? 0;

        // Hitung pemakaian sejak ganti terakhir per sparepart
        $pemakaianSekarang = 0;
        if ($data->isNotEmpty()) {
            $lastReplacement = \App\Models\PartReplacement::where('machine_id', $machine_id)
                ->latest('tanggal')
                ->first();
            $pemakaianSekarang = max(0, $counterTerakhir - ($lastReplacement->counter_saat_ganti ?? 0));
        }

        return view('cetak.part-per-mesin', compact(
            'machine',
            'data',
            'counterTerakhir',
            'pemakaianSekarang'
        ));
    }
}
