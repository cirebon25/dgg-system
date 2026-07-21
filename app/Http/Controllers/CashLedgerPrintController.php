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

        // 1. Ambil data transaksi kas bulanan dasar & saldo awal TERLEBIH DAHULU
        $transaksi = CashLedger::bulan($tahun, $bulan)->get();
        $saldoAwal = CashLedger::saldoAwalBulan($tahun, $bulan);

        // 2. Ambil semua data SPM (CashMutation) bulan ini beserta items-nya
        $spmData = CashMutation::with(['items'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // 3. Hitung running saldo per baris
        $saldo = $saldoAwal; // Saldo awal sudah aman digunakan di sini

        $rows = $transaksi->map(function ($item) use (&$saldo, $spmData) {
            $saldo += ($item->uang_masuk ?? 0) - ($item->uang_keluar ?? 0);

            $itemArray = $item->toArray();
            $itemsUraian = [];

            // Pencocokan otomatis dengan data SPM
            if (!empty($item->no_surat) || !empty($item->keterangan)) {
                $matchedSpm = $spmData->first(function ($spm) use ($item) {
                    $noVoucherClean = trim($spm->no_voucher ?? '');
                    if (empty($noVoucherClean)) return false;

                    $inNoSurat    = !empty($item->no_surat) && (strpos($item->no_surat, $noVoucherClean) !== false);
                    $inKeterangan = !empty($item->keterangan) && (strpos($item->keterangan, $noVoucherClean) !== false);

                    return $inNoSurat || $inKeterangan;
                });

                // Ambil SEMUA item uraian dari SPM yang cocok
                if ($matchedSpm && $matchedSpm->items && $matchedSpm->items->isNotEmpty()) {
                    $itemsUraian = $matchedSpm->items->pluck('uraian')->filter()->toArray();
                }
            }

            // Gabungkan sisa_saldo dan uraian_koma ke array baris
            return array_merge($itemArray, [
                'sisa_saldo'  => $saldo,
                'items'       => $itemsUraian,
                'uraian_koma' => !empty($itemsUraian) ? implode(', ', $itemsUraian) : null,
            ]);
        });

        $namaBulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return view('print.kas-bulanan', [
            'rows'        => $rows,
            'saldoAwal'   => $saldoAwal,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'namaBulan'   => $namaBulan[$bulan] ?? '',
            'totalMasuk'  => $transaksi->sum('uang_masuk'),
            'totalKeluar' => $transaksi->sum('uang_keluar'),
            'saldoAkhir'  => $saldo,
        ]);
    }
}
