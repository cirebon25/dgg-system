<?php

namespace App\Http\Controllers;

use App\Models\CashLedger;
use App\Models\CashMutation;
use Illuminate\Http\Request;

class CashLedgerPrintController extends Controller
{
    public function cetakBulanan(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        // 1. Ambil data transaksi kas bulanan dasar
        $transaksi = CashLedger::bulan($tahun, $bulan)->get();
        $saldoAwal = CashLedger::saldoAwalBulan($tahun, $bulan);

        // 2. Ambil semua data SPM (CashMutation) bulan ini beserta items-nya untuk dicocokkan
        $spmData = CashMutation::with(['items'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Hitung running saldo per baris
        $saldo = $saldoAwal;
        $rows  = $transaksi->map(function ($item) use (&$saldo, $spmData) {
            $saldo += $item->uang_masuk - $item->uang_keluar;
            
            $itemArray = $item->toArray();
            $uraianPertama = null;

            // Pencocokan otomatis:
            // Kita cari data SPM yang Nomor Vouchernya terkandung di dalam kolom 'no_surat' atau 'keterangan' milik CashLedger
            if ($item->no_surat || $item->keterangan) {
                $matchedSpm = $spmData->first(function ($spm) use ($item) {
                    $noVoucherClean = trim($spm->no_voucher);
                    if (empty($noVoucherClean)) return false;

                    return (strpos($item->no_surat, $noVoucherClean) !== false) || 
                           (strpos($item->keterangan, $noVoucherClean) !== false);
                });

                // Jika ketemu SPM yang cocok, ambil uraian dari baris pertama items-nya
                if ($matchedSpm && $matchedSpm->items && $matchedSpm->items->isNotEmpty()) {
                    $uraianPertama = $matchedSpm->items->first()->uraian;
                }
            }

            // Gabungkan sisa_saldo dan uraian ke data baris tabel
            return array_merge($itemArray, [
                'sisa_saldo' => $saldo,
                'uraian'     => $uraianPertama // Akan dibaca di Blade
            ]);
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