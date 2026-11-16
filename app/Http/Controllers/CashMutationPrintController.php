<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use Illuminate\Contracts\View\View;

class CashMutationPrintController extends Controller
{
    /**
     * Kapasitas baris uraian pada layout cetak A5 landscape.
     * 9 baris body dikurangi 3 baris tetap (PLAT / KM AWAL / KM AKHIR) = 6 slot item.
     */
    private const MAX_ITEMS = 6;

    /**
     * Route: Route::get('/print/cash-mutation/{cash_mutation}', ...)
     */
    public function print(CashMutation $cashMutation): View
    {
        $cashMutation->loadMissing('items');

        $items = $cashMutation->items;

        // Tolak cetak jika data tidak muat, daripada memotong baris secara diam-diam.
        if ($items->count() > self::MAX_ITEMS) {
            abort(
                422,
                'Bukti cetak hanya mendukung maksimal ' . self::MAX_ITEMS . ' item uraian. Pecah transaksi ini.'
            );
        }

        $tanggalStr = $cashMutation->tanggal
            ? 'Cirebon ,   ' . $cashMutation->tanggal->locale('id')->isoFormat('D MMMM Y')
            : 'Cirebon , ';

        $noVoucher = $cashMutation->no_voucher_lengkap
            ?: ('NO. ' . ($cashMutation->no_voucher ?? '-'));

        $total = (float) ($cashMutation->total_jumlah ?? $items->sum('jumlah'));

        $terbilang = trim($cashMutation->terbilang ?? '');
        if (! $terbilang && $total > 0) {
            $terbilang = strtoupper(CashMutation::konversiTerbilang($total) . ' RUPIAH');
        }
        if (! $terbilang) {
            $terbilang = '-';
        }

        // Data kendaraan untuk 3 baris tetap. Baris kosong dihitung di view
        // dari count($items), jadi tidak dikirim dari sini.
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
            'vehiclePlat'    => $vehicleItem->plat_nomor ?? null,
            'vehicleKmAwal'  => $vehicleItem->km_awal ?? null,
            'vehicleKmAkhir' => $vehicleItem->km_akhir ?? null,
        ]);
    }
}