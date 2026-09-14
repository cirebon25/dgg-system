<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages;

use App\Filament\Resources\PartBorrowingHeaderResource;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreatePartBorrowingHeader extends CreateRecord
{
    protected static string $resource = PartBorrowingHeaderResource::class;

    public function mount(): void
    {
        parent::mount();

        // Cek apakah halaman baru saja direfresh atau pertama kali dibuka
        // Jika tidak ada parameter redirect dari halaman password confirm, anggap sebagai akses baru/refresh
        if (! session()->has('auth.password_confirmed_at')) {
            session(['url.intended' => request()->url()]);
            $this->redirect(route('password.confirm'));
            return;
        }

        // Hapus sesi langsung setelah halaman berhasil dimuat, 
        // sehingga jika di-F5 lagi, dia akan langsung terkunci kembali
        session()->forget('auth.password_confirmed_at');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = $data['items'] ?? [];

        foreach ($items as $item) {
            $sparepartId = $item['sparepart_id'] ?? null;
            $jumlah      = (int) ($item['jumlah'] ?? 0);

            if (!$sparepartId || $jumlah <= 0) continue;

            $sparepart  = Sparepart::find($sparepartId);
            $stokGudang = $sparepart ? (int) $sparepart->stok : 0;

            if ($jumlah > $stokGudang) {
                Notification::make()
                    ->title('Pinjam Gagal!')
                    ->body("Stok \"{$sparepart->nama_sparepart}\" tidak cukup. Sisa: {$stokGudang} pcs.")
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $header = $this->getRecord();
        $header->load('items.sparepart', 'technician');

        // Array untuk menampung info saldo terbaru tiap item
        $ringkasanSaldo = [];

        DB::transaction(function () use ($header, &$ringkasanSaldo) {
            foreach ($header->items as $item) {
                $jumlah       = (int) $item->jumlah;
                $technicianId = $header->technician_id;
                $sparepartId  = $item->sparepart_id;

                // 1. Potong stok Gudang Utama
                $sparepart = Sparepart::find($sparepartId);
                if ($sparepart) {
                    $sparepart->decrement('stok', $jumlah);
                }

                // 2. Tambah ke Tas Teknisi
                $techStock = TechnicianStock::firstOrCreate(
                    [
                        'technician_id' => $technicianId,
                        'sparepart_id'  => $sparepartId,
                    ],
                    ['jumlah' => 0]
                );
                $techStock->increment('jumlah', $jumlah);
                $techStock->refresh();

                // 3. Catat riwayat mutasi
                TechnicianStockHistory::create([
                    'technician_id' => $technicianId,
                    'sparepart_id'  => $sparepartId,
                    'masuk'         => $jumlah,
                    'keluar'        => 0,
                    'saldo_akhir'   => (int) $techStock->jumlah,
                    'keterangan'    => 'Pinjam Part dari Gudang Utama',
                ]);

                // Simpan saldo akhir untuk referensi pesan WhatsApp
                $ringkasanSaldo[$sparepartId] = (int) $techStock->jumlah;
            }
        });

        session()->forget('auth.password_confirmed_at');

        Notification::make()
            ->title('Pinjam Berhasil! ✅')
            ->body("Stok gudang terpotong. Klik tombol Cetak di tabel untuk cetak bukti.")
            ->success()
            ->seconds(6)
            ->send();

        // Kirim data ringkasan saldo ke fungsi WhatsApp
        $this->kirimNotifikasiWhatsapp($header, $ringkasanSaldo);
    }

    // protected function afterCreate(): void
    // {
    //     $header = $this->getRecord();
    //     $header->load('items.sparepart', 'technician');

    //     DB::transaction(function () use ($header) {
    //         foreach ($header->items as $item) {
    //             $jumlah       = (int) $item->jumlah;
    //             $technicianId = $header->technician_id;
    //             $sparepartId  = $item->sparepart_id;

    //             // 1. Potong stok Gudang Utama
    //             $sparepart = Sparepart::find($sparepartId);
    //             if ($sparepart) {
    //                 $sparepart->decrement('stok', $jumlah);
    //             }

    //             // 2. Tambah ke Tas Teknisi
    //             $techStock = TechnicianStock::firstOrCreate(
    //                 [
    //                     'technician_id' => $technicianId,
    //                     'sparepart_id'  => $sparepartId,
    //                 ],
    //                 ['jumlah' => 0]
    //             );
    //             $techStock->increment('jumlah', $jumlah);
    //             $techStock->refresh();

    //             // 3. Catat riwayat mutasi
    //             TechnicianStockHistory::create([
    //                 'technician_id' => $technicianId,
    //                 'sparepart_id'  => $sparepartId,
    //                 'masuk'         => $jumlah,
    //                 'keluar'        => 0,
    //                 'saldo_akhir'   => (int) $techStock->jumlah,
    //                 'keterangan'    => 'Pinjam Part dari Gudang Utama',
    //             ]);
    //         }
    //     });

    //     // Hapus sesi konfirmasi password agar langsung terkunci kembali (1x transaksi)
    //     session()->forget('auth.password_confirmed_at');

    //     Notification::make()
    //         ->title('Pinjam Berhasil! ✅')
    //         ->body("Stok gudang terpotong. Klik tombol Cetak di tabel untuk cetak bukti.")
    //         ->success()
    //         ->seconds(6)
    //         ->send();

    //     $this->kirimNotifikasiWhatsapp($header);
    // }

    // protected function kirimNotifikasiWhatsapp($header): void
    // {
    //     $technician = $header->technician;

    //     if (!$technician || empty($technician->nomor_hp)) {
    //         return; // tidak ada nomor HP, skip kirim WA
    //     }

    //     $daftarPart = $header->items->map(
    //         fn($item) => "- {$item->sparepart->nama_sparepart} x{$item->jumlah}"
    //     )->implode("\n");

    //     $message = "Halo {$technician->nama_technician},\n\n"
    //         . "Anda baru saja meminjam sparepart berikut dari Gudang Utama:\n"
    //         . "{$daftarPart}\n\n"
    //         . "Keterangan: " . ($header->keterangan ?: '-') . "\n\n"
    //         . "Mohon dikonfirmasi. Terima kasih.";

    //     try {
    //         Http::timeout(10)->post('http://localhost:3001/send', [
    //             'number'  => $technician->nomor_hp,
    //             'message' => $message,
    //         ]);
    //     } catch (\Throwable $e) {
    //         Log::warning('Gagal kirim WA notifikasi peminjaman: ' . $e->getMessage());
    //     }
    // }

    protected function kirimNotifikasiWhatsapp($header): void
    {
        $technician = $header->technician;

        if (!$technician || empty($technician->nomor_hp)) {
            return;
        }

        // 1. Ambil daftar part yang baru dipinjam pada transaksi ini
        $daftarPinjam = $header->items->map(function ($item) {
            return "- {$item->sparepart->nama_sparepart} (+{$item->jumlah} pcs)";
        })->implode("\n");

        // 2. Ambil SEMUA saldo sparepart yang saat ini ada di tas teknisi tersebut
        $semuaStokTas = TechnicianStock::with('sparepart')
            ->where('technician_id', $technician->id)
            ->where('jumlah', '>', 0) // Opsional: Hanya tampilkan yang saldonya masih ada (> 0)
            ->get();

        $daftarSemuaStok = $semuaStokTas->map(function ($stock) {
            return "- {$stock->sparepart->nama_sparepart}: *{$stock->jumlah} pcs*";
        })->implode("\n");

        // 3. Susun isi pesan WhatsApp
        $message = "Halo {$technician->nama_technician},\n\n"
            . "📦 *RINCIAN PEMINJAMAN BARU*:\n"
            . "{$daftarPinjam}\n\n"
            . "Keterangan: " . ($header->keterangan ?: '-') . "\n\n"
            . "📋 *TOTAL SALDO DI TAS ANDA SAAT INI*:\n"
            . ($daftarSemuaStok ?: "- Kosong") . "\n\n"
            . "Mohon untuk dicek kembali. Terima kasih.";

        try {
            Http::timeout(10)->post('http://localhost:3001/send', [
                'number'  => $technician->nomor_hp,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WA notifikasi peminjaman: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return route('cetak.bukti-pinjam-multi', $this->getRecord()->id);
    }
}