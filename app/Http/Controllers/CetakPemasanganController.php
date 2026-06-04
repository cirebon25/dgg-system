<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakPemasanganController extends Controller
{
    public function index($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

       // Sesudah
$data = Deployment::with(['machine', 'customer.rayon', 'technician'])
    ->whereMonth('created_at', $bulan)
    ->whereYear('created_at', $tahun)
    // Exclude deployment yang machine_id-nya ada di machine_replacements sebagai new_machine_id
    ->whereNotIn('machine_id', function ($query) {
        $query->select('new_machine_id')->from('machine_replacements');
    })
    ->orderBy('created_at', 'asc')
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
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];
        $namaBulan = $bulanIndo[$bulan] ?? 'Tidak Diketahui';

        return view('cetak.pemasangan-baru', compact(
            'data',
            'sparepartPerDeployment',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }
}
