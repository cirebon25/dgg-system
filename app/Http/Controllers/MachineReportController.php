<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineReportController extends Controller
{
    public function rekapUnitCustomer()
    {
        // 1. Ambil semua tipe_model mesin unik dari DB untuk header kolom
        $listSeri = DB::table('machines')
            ->whereNotNull('tipe_model')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('tipe_model')
            ->sort()
            ->values();

        // 2. Query data unit aktif (Ambil data berdasarkan model / tabel relasi yang aman)
        // Kita join langsung ke tabel customers tanpa menarik kolom namanya di group by SQL untuk menghindari error
        $rekapData = DB::table('deployments')
            ->join('machines', 'deployments.machine_id', '=', 'machines.id')
            ->whereNull('deployments.tanggal_tarik') 
            ->whereNull('deployments.deleted_at')
            ->select(
                'deployments.customer_id',
                'machines.tipe_model',
                DB::raw('count(machines.id) as total_unit')
            )
            ->groupBy('deployments.customer_id', 'machines.tipe_model')
            ->get();

        // 3. Ambil data master nama customer untuk dicocokkan di PHP (Aman dari error nama kolom di SQL Group By)
        $masterCustomer = DB::table('customers')->get();

        // 4. Mapping data ke dalam bentuk matriks horizontal
        $matrix = [];
        foreach ($rekapData as $data) {
            if (!isset($matrix[$data->customer_id])) {
                // Cari data customer berdasarkan ID-nya di koleksi master
                $customer = $masterCustomer->firstWhere('id', $data->customer_id);
                
                // Gunakan nama yang ada (mendukung kolom 'name', 'nama', atau 'nama_customer' secara otomatis)
                $namaCustomer = $customer->name ?? $customer->nama ?? $customer->nama_customer ?? 'Customer ID: ' . $data->customer_id;

                $matrix[$data->customer_id] = [
                    'nama_customer' => $namaCustomer,
                    'seri' => array_fill_keys($listSeri->toArray(), 0),
                    'total_per_customer' => 0
                ];
            }
            $matrix[$data->customer_id]['seri'][$data->tipe_model] = $data->total_unit;
            $matrix[$data->customer_id]['total_per_customer'] += $data->total_unit;
        }

        // 5. Urutkan baris berdasarkan nama customer agar rapi dari A-Z
        uasort($matrix, function($a, $b) {
            return strcmp($a['nama_customer'], $b['nama_customer']);
        });

        // 6. Return ke file blade
        return view('reports.rekap-mesin-customer', compact('matrix', 'listSeri'));
    }
}