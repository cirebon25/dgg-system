<?php

namespace App\Http\Controllers;

use App\Models\CashLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CetakKasMingguanController extends Controller
{
    public function __invoke(Request $request)
    {
        // Cek apakah dikirim dari Pusat Cetak (menggunakan bulan & tahun)
        if ($request->has('bulan') && $request->has('tahun')) {
            $bulan = $request->input('bulan', now()->month);
            $tahun = $request->input('tahun', now()->year);

            $mulai   = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $selesai = $mulai->copy()->endOfMonth()->startOfDay();
        } else {
            // Jika dikirim dari form filter tanggal kustom (mulai & selesai)
            $request->validate([
                'mulai'   => 'required|date',
                'selesai' => 'required|date|after_or_equal:mulai',
            ]);

            $mulai   = Carbon::parse($request->mulai)->startOfDay();
            $selesai = Carbon::parse($request->selesai)->startOfDay();
        }

        // Saldo awal = saldo awal bulan + mutasi dari tgl 1 s/d sehari sebelum tanggal mulai
        $awalBulan = $mulai->copy()->startOfMonth();
        $saldoAwal = CashLedger::saldoAwalBulan($mulai->year, $mulai->month);

        if ($mulai->gt($awalBulan)) {
            $sebelum = CashLedger::whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $mulai->copy()->subDay()->toDateString(),
            ]);

            $saldoAwal += (clone $sebelum)->sum('uang_masuk')
                - (clone $sebelum)->sum('uang_keluar');
        }

        $data = CashLedger::with(['cashMutation.items', 'cashReceipt'])
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $saldo = $saldoAwal;

        $rows = $data->map(function ($r) use (&$saldo) {
            $saldo += $r->uang_masuk - $r->uang_keluar;

            $uraianKoma = null;
            if ($r->cashMutation && $r->cashMutation->items->isNotEmpty()) {
                $uraianKoma = $r->cashMutation->items->pluck('uraian')->filter()->implode(', ');
            } elseif ($r->cashReceipt) {
                $uraianKoma = $r->cashReceipt->uraian ?? $r->cashReceipt->keterangan;
            }

            return [
                'tanggal'     => $r->tanggal,
                'no_surat'    => $r->no_surat,
                'uraian_koma' => $uraianKoma,
                'keterangan'  => $r->keterangan,
                'uang_masuk'  => (float) $r->uang_masuk,
                'uang_keluar' => (float) $r->uang_keluar,
                'sisa_saldo'  => $saldo,
            ];
        });

        return view('cetak.kas-mingguan', [
            'rows'         => $rows,
            'saldoAwal'    => $saldoAwal,
            'totalMasuk'   => $rows->sum('uang_masuk'),
            'totalKeluar'  => $rows->sum('uang_keluar'),
            'saldoAkhir'   => $saldo,
            'mulai'        => $mulai,
            'selesai'      => $selesai,
            'periodeLabel' => $mulai->isoFormat('D MMMM Y') . ' s/d ' . $selesai->isoFormat('D MMMM Y'),
        ]);
    }
}
