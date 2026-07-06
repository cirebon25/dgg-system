<?php

namespace App\Observers;

use App\Models\CashMutation;
use App\Models\CashLedger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashMutationObserver
{
    // -------------------------------------------------------
    // Format no_voucher "2 / VI / 26" → "02/VI/26"
    // -------------------------------------------------------
    private function formatNoSurat(CashMutation $mutation): string
    {
        if (empty($mutation->no_voucher)) return '';

        $parts = array_map('trim', explode('/', $mutation->no_voucher));

        return str_pad((int) ($parts[0] ?? 1), 2, '0', STR_PAD_LEFT)
            . '/' . ($parts[1] ?? '')
            . '/' . ($parts[2] ?? '');
    }

    // -------------------------------------------------------
    // Ambil uraian dari items SPM, fallback ke no_voucher
    // -------------------------------------------------------
    private function buildKeterangan(CashMutation $mutation, string $tipe): string
    {
        $uraian = $mutation->items()
            ->pluck('uraian')
            ->filter()
            ->implode(', ');

        return filled($uraian)
            ? "[{$tipe}] " . $uraian
            : "[{$tipe}] Voucher No: " . ($mutation->no_voucher ?? '');
    }

    // -------------------------------------------------------
    // Tentukan masuk/keluar berdasarkan no_voucher atau terbilang
    // -------------------------------------------------------
    private function resolveJenis(CashMutation $mutation): array
    {
        $noVoucher = strtoupper((string) ($mutation->no_voucher ?? ''));
        $terbilang = strtoupper((string) ($mutation->terbilang ?? ''));

        $isMasuk = str_contains($noVoucher, 'MASUK')
            || str_contains($terbilang, 'MASUK');

        return [
            'uang_masuk'  => $isMasuk ? (int) $mutation->total_jumlah : 0,
            'uang_keluar' => $isMasuk ? 0 : (int) $mutation->total_jumlah,
            'tipe'        => $isMasuk ? 'Saldo Masuk' : 'SPM',
        ];
    }

    // -------------------------------------------------------
    // Cari ledger — coba exact match dulu, fallback ke LIKE
    // -------------------------------------------------------
    private function findLedger(string $noSurat): ?CashLedger
    {
        return CashLedger::where('no_surat', $noSurat)->first()
            ?? CashLedger::where('keterangan', 'LIKE', '%' . trim($noSurat) . '%')->first();
    }

    // -------------------------------------------------------
    // CREATED
    // -------------------------------------------------------
    public function created(CashMutation $cashMutation): void
    {
        DB::transaction(function () use ($cashMutation) {
            // Guard presisi: 1 CashMutation hanya boleh punya 1 CashLedger.
            // Cek berdasarkan cash_mutation_id (bukan no_surat) supaya tidak
            // memblokir voucher lain yang kebetulan punya nomor urut sama.
            if (CashLedger::where('cash_mutation_id', $cashMutation->id)->exists()) {
                \Illuminate\Support\Facades\Log::warning('CashMutationObserver: skip double insert', [
                    'cash_mutation_id' => $cashMutation->id,
                    'no_surat'         => $this->formatNoSurat($cashMutation),
                ]);
                return;
            }

            $jenis   = $this->resolveJenis($cashMutation);
            $tanggal = $cashMutation->tanggal
                ? Carbon::parse($cashMutation->tanggal)->format('Y-m-d')
                : now()->format('Y-m-d');

            CashLedger::create([
                'cash_mutation_id' => $cashMutation->id,
                'tanggal'          => $tanggal,
                'no_surat'         => $this->formatNoSurat($cashMutation),
                'keterangan'       => $this->buildKeterangan($cashMutation, $jenis['tipe']),
                'uang_masuk'       => $jenis['uang_masuk'],
                'uang_keluar'      => $jenis['uang_keluar'],
                'dibuat_oleh'      => $cashMutation->pembuat ?? auth()->user()?->name ?? 'SYSTEM',
            ]);
        });
    }

    // -------------------------------------------------------
    // UPDATED
    // -------------------------------------------------------
    public function updated(CashMutation $cashMutation): void
    {
        DB::transaction(function () use ($cashMutation) {
            $oldVoucher    = (string) ($cashMutation->getOriginal('no_voucher') ?? $cashMutation->no_voucher ?? '');
            $oldNoSurat    = $this->formatNoSurat(tap(clone $cashMutation, fn($m) => $m->no_voucher = $oldVoucher));

            if (empty($oldVoucher)) return;

            $jenis      = $this->resolveJenis($cashMutation);
            $tanggal    = $cashMutation->tanggal
                ? Carbon::parse($cashMutation->tanggal)->format('Y-m-d')
                : now()->format('Y-m-d');
            $newNoSurat  = $this->formatNoSurat($cashMutation);
            $keterangan  = $this->buildKeterangan($cashMutation, $jenis['tipe']);

            $ledger = $this->findLedger($oldNoSurat)
                ?? $this->findLedger($oldVoucher);

            if ($ledger) {
                $ledger->update([
                    'tanggal'     => $tanggal,
                    'no_surat'    => $newNoSurat,
                    'keterangan'  => $keterangan,
                    'uang_masuk'  => $jenis['uang_masuk'],
                    'uang_keluar' => $jenis['uang_keluar'],
                ]);
            }
            // else dihapus — tidak boleh create dari updated
        });
    }

    // -------------------------------------------------------
    // DELETED
    // -------------------------------------------------------
    public function deleted(CashMutation $cashMutation): void
    {
        DB::transaction(function () use ($cashMutation) {
            $noSurat   = $this->formatNoSurat($cashMutation);
            $noVoucher = (string) ($cashMutation->no_voucher ?? '');

            CashLedger::where('no_surat', $noSurat)
                ->orWhere('no_surat', $noVoucher)
                ->orWhere('keterangan', 'LIKE', '%' . trim($noVoucher) . '%')
                ->delete();
        });
    }
}/*  */