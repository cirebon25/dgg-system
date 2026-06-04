<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SaldoSparepartController extends Controller
{
    public function index()
    {
        $spareparts = DB::table('spareparts as sp')
            ->select(
                'sp.no_part',
                'sp.code_part',
                'sp.nama_sparepart',
                DB::raw('COALESCE(sp.stok, 0) AS stok_aktif'),
                DB::raw('COALESCE(ts_sum.total_tas, 0) AS stok_tas'),
                DB::raw('COALESCE(sp.stok, 0) + COALESCE(ts_sum.total_tas, 0) AS total_saldo')
            )
            ->leftJoin(
                // SUM kolom `jumlah` dari technician_stocks, group by sparepart_id
                DB::raw('(
                    SELECT sparepart_id, SUM(jumlah) AS total_tas
                    FROM technician_stocks
                    GROUP BY sparepart_id
                ) AS ts_sum'),
                'ts_sum.sparepart_id', '=', 'sp.id'
            )
            ->orderByRaw("CASE WHEN sp.no_part LIKE 'S%' THEN 0 ELSE 1 END")
            ->orderBy('sp.no_part', 'asc')
            ->get();

        $summary = [
            'total_item'  => $spareparts->count(),
            'total_aktif' => $spareparts->sum('stok_aktif'),
            'total_tas'   => $spareparts->sum('stok_tas'),
            'total_saldo' => $spareparts->sum('total_saldo'),
            'item_kosong' => $spareparts->where('total_saldo', '<=', 0)->count(),
        ];

        $tanggalCetak = now()->format('d/m/Y H:i');

        return view('sparepart.saldo', compact('spareparts', 'summary', 'tanggalCetak'));
    }
}