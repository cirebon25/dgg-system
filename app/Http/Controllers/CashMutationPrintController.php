<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use Illuminate\Contracts\View\View;

class CashMutationPrintController extends Controller
{
    /**
     * FIX: implicit route model binding menggantikan `print($id)` + findOrFail manual.
     * Route: Route::get('/print/cash-mutation/{cash_mutation}', ...)
     */
    public function print(CashMutation $cashMutation): View
    {
        $cashMutation->loadMissing('items');

        // FIX: tidak lagi parse ulang tanggal manual — kolom sudah di-cast Carbon
        // di model ('tanggal' => 'date:Y-m-d'), jadi $cashMutation->tanggal sudah
        // pasti instance Carbon (atau null). try/catch Carbon::parse lama adalah dead code.
        $tanggalStr = $cashMutation->tanggal
            ? 'Cirebon ,   ' . $cashMutation->tanggal->locale('id')->isoFormat('D MMMM Y')
            : 'Cirebon , ';

        // FIX: nomor voucher lengkap sekarang diambil dari accessor model
        // (getNoVoucherLengkapAttribute), bukan direkonstruksi ulang di controller.
        // Fallback pengaman: kalau accessor belum ter-load (model lama) atau
        // hasilnya kosong, tetap tampilkan sesuatu yang informatif, bukan blank.
        $noVoucher = $cashMutation->no_voucher_lengkap
            ?: ('NO. ' . ($cashMutation->no_voucher ?? '-'));

        $items = $cashMutation->items;
        $total = (float) ($cashMutation->total_jumlah ?? $items->sum('jumlah'));

        $terbilang = trim($cashMutation->terbilang ?? '');
        if (!$terbilang && $total > 0) {
            $terbilang = strtoupper(CashMutation::konversiTerbilang($total) . ' RUPIAH');
        }
        if (!$terbilang) {
            $terbilang = '-';
        }

        // Baris kosong pengisi tabel, supaya cetakan tetap pas 1 halaman A5 landscape.
        // Base diturunkan dari 10 -> 6 karena sekarang selalu ada 3 baris tetap
        // tambahan (PLAT / KM AWAL / KM AKHIR) di bawah tabel, sesuai dokumen fisik.
        $emptyRows = max(0, 5 - count($items));

        // Data kendaraan untuk 3 baris tetap: ambil dari item pertama yang mengisi
        // salah satu field ini (biasanya hanya 1 item transport per voucher).
        $vehicleItem = $items->first(
            fn($item) => $item->plat_nomor || $item->km_awal || $item->km_akhir
        );

        return view('print.cash-mutation', [
            'data'           => $cashMutation,
            'noVoucher'      => $noVoucher,
            'tanggalStr'     => $tanggalStr,
            'total'          => $total,
            'terbilang'      => $terbilang,
            'items'          => $items,
            'emptyRows'      => $emptyRows,
            'vehiclePlat'    => $vehicleItem->plat_nomor ?? null,
            'vehicleKmAwal'  => $vehicleItem->km_awal ?? null,
            'vehicleKmAkhir' => $vehicleItem->km_akhir ?? null,
        ]);
    }
}