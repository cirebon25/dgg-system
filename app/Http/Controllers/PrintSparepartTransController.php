<?php

namespace App\Http\Controllers;

use App\Models\MachineReplacement;
use App\Models\Technician;
use App\Models\TechnicianStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintSparepartTransController extends Controller
{
    // dipindah dari: Route::get('/cetak-bukti-pinjam/{id}', ...)->name('cetak.bukti-pinjam')
    public function buktiPinjam($id)
    {
        $loan = DB::table('part_borrowings')
            ->join('spareparts', 'part_borrowings.sparepart_id', '=', 'spareparts.id')
            ->join('technicians', 'part_borrowings.technician_id', '=', 'technicians.id')
            ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')
            ->where('part_borrowings.id', $id)
            ->select(
                'part_borrowings.*',
                'spareparts.nama_sparepart',
                'spareparts.code_part',
                'technicians.nama_technician',
                DB::raw("COALESCE(rayons.nama_rayon, 'BARAT DAYA') as nama_rayon")
            )
            ->first();

        if (!$loan) {
            return "Bukti Transaksi Tidak Ditemukan!";
        }

        $sisaSaldo = DB::table('technician_stocks')
            ->where('technician_id', $loan->technician_id)
            ->where('sparepart_id', $loan->sparepart_id)
            ->value('jumlah') ?? 0;

        return view('print.bukti-pinjam', compact('loan', 'sisaSaldo'));
    }

    // CATATAN PERBAIKAN (24 Juni 2026):
    // Route ini dulunya menerima query string ?payload=... (base64 JSON), sesuai
    // closure asli di web.php. Namun App\Filament\Pages\GantiMesin::submit() sudah
    // diubah untuk memanggil route('cetak.sj-rolling', $replacementId) — yaitu
    // mengirim ID record machine_replacements langsung sebagai parameter, BUKAN
    // payload base64 lagi. Method ini disesuaikan agar sinkron dengan cara
    // pemanggilan yang sekarang dipakai GantiMesin, dan agar cocok dengan variabel
    // yang dibutuhkan oleh resources/views/cetak/surat-jalan-rolling.blade.php
    // (yang membutuhkan $replacement, $tanggal, $nomor_sj).
    public function sjRolling($replacementId)
    {
        $replacement = MachineReplacement::with([
            'customer',
            'oldMachine',
            'newMachine',
            'technician',
            'deployment.deploymentSpareparts.sparepart',
        ])->find($replacementId);

        if (!$replacement) {
            return 'Gagal memuat dokumen! Data Surat Jalan tidak ditemukan. Silakan ulangi proses rolling dari menu Ganti Mesin';
        }

        return view('cetak.surat-jalan-rolling', [
            'replacement' => $replacement,
            'tanggal'     => \Carbon\Carbon::parse($replacement->tanggal)->format('d/m/Y'),
            'nomor_sj'    => 'SJ-RR/' . \Carbon\Carbon::parse($replacement->tanggal)->format('Ymd') . '/' . str_pad($replacement->id, 3, '0', STR_PAD_LEFT),
        ]);
    }

    // dipindah dari: Route::get('/cetak-bukti-pinjam-multi/{id}', ...)->name('cetak.bukti-pinjam-multi')
    public function buktiPinjamMulti($id)
    {
        $header = DB::table('part_borrowing_headers')
            ->join('technicians', 'part_borrowing_headers.technician_id', '=', 'technicians.id')
            ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')
            ->where('part_borrowing_headers.id', $id)
            ->select(
                'part_borrowing_headers.*',
                'technicians.nama_technician',
                DB::raw("COALESCE(rayons.nama_rayon, 'PUSAT') as nama_rayon")
            )
            ->first();

        if (!$header) {
            return "Transaksi tidak ditemukan!";
        }

        $items = DB::table('part_borrowing_items')
            ->join('spareparts', 'part_borrowing_items.sparepart_id', '=', 'spareparts.id')
            ->where('part_borrowing_items.part_borrowing_header_id', $id)
            ->select(
                'part_borrowing_items.*',
                'spareparts.nama_sparepart',
                'spareparts.code_part'
            )
            ->get();

        $saldoTeknisi = [];
        foreach ($items as $item) {
            $saldoTeknisi[$item->sparepart_id] = DB::table('technician_stocks')
                ->where('technician_id', $header->technician_id)
                ->where('sparepart_id', $item->sparepart_id)
                ->value('jumlah') ?? 0;
        }

        return view('print.bukti-pinjam-multi', compact('header', 'items', 'saldoTeknisi'));
    }

    // dipindah dari: Route::get('/cetak-kartu-stok/{technician_id}', ...)->name('cetak.kartu-stok')
    public function kartuStok($technician_id)
    {
        $teknisi = Technician::findOrFail($technician_id);
        $stocks  = TechnicianStock::with('sparepart')
            ->where('technician_id', $technician_id)
            ->where('jumlah', '>', 0)
            ->get();
        $total = $stocks->sum('jumlah');

        return view('print.kartu-stok', compact('teknisi', 'stocks', 'total'));
    }

    // dipindah dari: Route::get('/cetak-kartu-stok-semua', ...)->name('cetak.kartu-stok-semua')
    public function kartuStokSemua()
    {
        $data = Technician::with(['technicianStocks.sparepart'])
            ->whereHas('technicianStocks', fn($q) => $q->where('jumlah', '>', 0))
            ->get()
            ->map(fn($t) => [
                'teknisi' => $t,
                'stocks'  => $t->technicianStocks->where('jumlah', '>', 0),
                'total'   => $t->technicianStocks->where('jumlah', '>', 0)->sum('jumlah'),
            ]);

        return view('print.kartu-stok-semua', compact('data'));
    }

    // Kartu stok per sparepart (tracking masuk/keluar + saldo berjalan)
    public function kartuStokSparepart($sparepart_id)
    {
        $sparepart = \App\Models\Sparepart::findOrFail($sparepart_id);

        $transaksi = collect();

        // 1. MASUK — dari supplier (sparepart_entries)
        $masuk = DB::table('sparepart_entries')
            ->where('sparepart_id', $sparepart_id)
            ->orderBy('created_at')
            ->get(['jumlah', 'created_at', 'supplier']);

        foreach ($masuk as $m) {
            $transaksi->push([
                'tanggal'    => $m->created_at,
                'keterangan' => 'Masuk dari Supplier: ' . ($m->supplier ?? '-'),
                'masuk'      => $m->jumlah,
                'keluar'     => 0,
                'tipe'       => 'MASUK',
            ]);
        }

        // 2. KELUAR — drop ke teknisi (technician_stock_histories keluar > 0)
        $keluar = DB::table('technician_stock_histories as h')
            ->join('technicians as t', 't.id', '=', 'h.technician_id')
            ->where('h.sparepart_id', $sparepart_id)
            ->orderBy('h.created_at')
            ->get(['h.masuk', 'h.keluar', 'h.keterangan', 'h.created_at', 't.nama_technician']);

        foreach ($keluar as $k) {
            if ($k->keluar > 0) {
                $transaksi->push([
                    'tanggal'    => $k->created_at,
                    'keterangan' => 'Keluar ke Teknisi: ' . $k->nama_technician . ($k->keterangan ? ' — ' . $k->keterangan : ''),
                    'masuk'      => 0,
                    'keluar'     => $k->keluar,
                    'tipe'       => 'KELUAR',
                ]);
            }
            if ($k->masuk > 0) {
                $transaksi->push([
                    'tanggal'    => $k->created_at,
                    'keterangan' => 'Kembali dari Teknisi: ' . $k->nama_technician . ($k->keterangan ? ' — ' . $k->keterangan : ''),
                    'masuk'      => $k->masuk,
                    'keluar'     => 0,
                    'tipe'       => 'RETUR',
                ]);
            }
        }

        // Urutkan berdasarkan tanggal
        $transaksi = $transaksi->sortBy('tanggal')->values();

        // Hitung saldo berjalan
        $saldo = 0;
        $transaksi = $transaksi->map(function ($t) use (&$saldo) {
            $saldo += $t['masuk'] - $t['keluar'];
            $t['saldo'] = $saldo;
            return $t;
        });

        return view('print.kartu-stok-sparepart', compact('sparepart', 'transaksi'));
    }
}
