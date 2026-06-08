<?php

namespace App\Http\Controllers;

use App\Models\CashLedger;
use Illuminate\Http\Request;

class CashLedgerPrintController extends Controller
{
    public function cetakBulanan(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $transaksi = CashLedger::bulan($tahun, $bulan)->get();
        $saldoAwal = CashLedger::saldoAwalBulan($tahun, $bulan);

        // Hitung running saldo per baris
        $saldo = $saldoAwal;
        $rows  = $transaksi->map(function ($item) use (&$saldo) {
            $saldo += $item->uang_masuk - $item->uang_keluar;
            return array_merge($item->toArray(), ['sisa_saldo' => $saldo]);
        });

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

        return view('print.kas-bulanan', [
            'rows'       => $rows,
            'saldoAwal'  => $saldoAwal,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'namaBulan'  => $namaBulan[$bulan],
            'totalMasuk' => $transaksi->sum('uang_masuk'),
            'totalKeluar' => $transaksi->sum('uang_keluar'),
            'saldoAkhir' => $saldo,
        ]);
    }
}
