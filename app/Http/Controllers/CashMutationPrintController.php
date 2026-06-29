<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashMutationPrintController extends Controller
{
    public function print($id)
    {
        $data = CashMutation::with(['items'])->findOrFail($id);

        // ── Tanggal ──
        $tgl = null;
        try {
            if ($data->tanggal) {
                $tgl = $data->tanggal instanceof Carbon
                    ? $data->tanggal
                    : Carbon::parse($data->tanggal);
            }
        } catch (\Exception $e) {
        }

        // ── No Voucher ──
        $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        if ($tgl) {
            $seq       = $data->no_urut ?? (explode('/', $data->no_voucher ?? '')[0] ?? '');
            $noVoucher = 'NO. ' . trim($seq) . ' / ' . $bulanRomawi[$tgl->month - 1] . ' / ' . $tgl->format('y');
        } else {
            $noVoucher = 'NO. ' . ($data->no_voucher ?? '-');
        }

        // ── Tanggal string ──
        $tanggalStr = $tgl
            ? 'Cirebon ,   ' . $tgl->locale('id')->isoFormat('D MMMM Y')
            : 'Cirebon , ';

        // ── Total ──
        $total = (float) ($data->total_jumlah ?? $data->items->sum('jumlah') ?? 0);

        // ── Terbilang (hitung di controller, bukan di blade) ──
        $terbilang = trim($data->terbilang ?? '');
        if (!$terbilang && $total > 0) {
            $terbilang = strtoupper(CashMutation::konversiTerbilang($total) . ' RUPIAH');
        }
        if (!$terbilang) $terbilang = '-';

        // ── Items & empty rows ──
        // CATATAN: emptyRows diperkecil ke 2 karena detail kendaraan (Plat No,
        // KM Awal, KM Akhir) sekarang dipecah jadi 3 baris terpisah per item,
        // sehingga tabel jadi lebih tinggi. Mengurangi baris kosong menjaga
        // dokumen tetap pas 1 halaman A5 landscape.
        $items     = $data->items ?? collect();
        $emptyRows = max(0, 10 - count($items));

        return view('print.cash-mutation', compact(
            'data',
            'noVoucher',
            'tanggalStr',
            'total',
            'terbilang',
            'items',
            'emptyRows'
        ));
    }
}
