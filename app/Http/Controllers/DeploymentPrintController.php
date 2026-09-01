<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DeploymentPrintController extends Controller
{
    // [PENJELASAN] Memproses request cetak HTTP murni tanpa komponen Livewire
    // [ALUR] Dipanggil dari route GET /admin/deployments/print-yearly
    public function printYearly(Request $request)
    {
        $request->validate([
            'year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:' . (date('Y') + 1)],
        ]);

        $endYear = (int) $request->query('year');
        $startYear = $endYear - 4;

        $deployments = Deployment::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('count(*) as count')
        )
            ->whereBetween(DB::raw('YEAR(created_at)'), [$startYear, $endYear])
            ->whereNotIn('id', function ($query) {
                $query->select('deployment_id')->from('machine_replacements')->whereNotNull('deployment_id');
            })
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->pluck('count', 'year')
            ->toArray();

        $reportData = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $reportData[$year] = $deployments[$year] ?? 0;
        }

        // [PERBAIKAN] Menggunakan view cetak terpisah yang steril dari variabel objek Livewire $this
        return view('filament.widgets.yearly-deployment-print', [
            'reportData' => $reportData,
            'startYear'  => $startYear,
            'endYear'    => $endYear,
        ]);
    }

    public function printServiceCard(Deployment $deployment)
    {
        $deployment->loadMissing(['customer', 'machine']);

        $pdf = Pdf::loadView('pdf.service-card', [
            'nama_perusahaan' => $deployment->customer->nama_customer,
            'alamat'          => $deployment->customer->alamat,
            'tgl_instal'      => $deployment->tanggal_instal?->format('d/m/Y'),
            'merk_type'       => $deployment->machine->tipe_model,
            'no_seri'         => $deployment->machine->serial_number,
            'voltage'         => $deployment->volt,
        ])->setPaper('a4', 'landscape');

        // Buat file base64 di sini, BUKAN di view service-card
        return view('pdf.print-wrapper', [
            'base64' => base64_encode($pdf->output()),
        ]);
    }
}