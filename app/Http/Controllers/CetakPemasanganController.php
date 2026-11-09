<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CetakPemasanganController extends Controller
{
    public function index($bulan = null, $tahun = null)
    {
        $bulan = str_pad((string) ($bulan ?? date('m')), 2, '0', STR_PAD_LEFT);
        $tahun = (int) ($tahun ?? date('Y'));

        // Validasi bulan & tahun
        abort_unless(
            ctype_digit($bulan) && (int) $bulan >= 1 && (int) $bulan <= 12 && $tahun >= 2000 && $tahun <= 2100,
            404
        );

        $awal  = Carbon::create($tahun, (int) $bulan, 1)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth();

        // Filter berdasarkan tanggal_instal (sama dengan "Tgl Pasang" di resource)
        $data = Deployment::with(['machine', 'customer', 'technician'])
            ->whereBetween('tanggal_instal', [$awal->toDateString(), $akhir->toDateString()])
            // Exclude mesin pengganti (new_machine_id di machine_replacements)
            ->whereNotIn('machine_id', function ($query) {
                $query->select('new_machine_id')
                    ->from('machine_replacements')
                    ->whereNotNull('new_machine_id');
            })
            ->orderBy('tanggal_instal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Ambil sparepart per deployment sekaligus (hindari N+1 query)
        $sparepartPerDeployment = DB::table('deployment_sparepart')
            ->join('spareparts', 'deployment_sparepart.sparepart_id', '=', 'spareparts.id')
            ->whereIn('deployment_sparepart.deployment_id', $data->pluck('id'))
            ->select(
                'deployment_sparepart.deployment_id',
                'spareparts.nama_sparepart',
                'deployment_sparepart.jumlah'
            )
            ->get()
            ->groupBy('deployment_id');

        $bulanIndo = [
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
        $namaBulan = $bulanIndo[$bulan];

        return view('cetak.pemasangan-baru', compact(
            'data',
            'sparepartPerDeployment',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }
}