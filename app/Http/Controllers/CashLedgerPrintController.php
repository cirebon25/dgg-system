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

        // Ambil transaksi + eager load relasi cashMutation.items & cashReceipt
        // Urutan SAMA PERSIS dengan Filament Resource: tanggal asc, id asc
        // dan items() diurutkan sesuai urutan input (id asc)
        $transaksi = CashLedger::with([
            'cashMutation.items' => fn($q) => $q->orderBy('id', 'asc'),
            'cashReceipt',
        ])
            ->bulan($tahun, $bulan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $saldoAwal = CashLedger::saldoAwalBulan($tahun, $bulan);

        // Hitung running saldo per baris, ambil uraian LANGSUNG dari relasi
        // record (bukan pencocokan teks), sama seperti kolom di Resource.
        $saldo = $saldoAwal;
        $rows = $transaksi->map(function ($item) use (&$saldo) {
            $saldo += ($item->uang_masuk ?? 0) - ($item->uang_keluar ?? 0);

            $itemsUraian = [];

            if ($item->cashMutation && $item->cashMutation->items->isNotEmpty()) {
                // items sudah diurutkan by id asc via eager load di atas
                $itemsUraian = $item->cashMutation->items
                    ->pluck('uraian')
                    ->filter()
                    ->toArray();
            } elseif ($item->cashReceipt) {
                $uraian = $item->cashReceipt->uraian ?? $item->cashReceipt->keterangan ?? null;
                if ($uraian) {
                    $itemsUraian = [$uraian];
                }
            }

            $itemArray = $item->toArray();

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
