<?php

namespace App\Filament\Resources\PartReturnResource\Pages;

use App\Filament\Resources\PartReturnResource;
use App\Models\PartReturn;
use App\Models\Sparepart;
use App\Models\Technician;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreatePartReturn extends CreateRecord
{
    protected static string $resource = PartReturnResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $techId = $data['technician_id'] ?? null;
        $items  = $data['items'] ?? [];

        // Validasi stok semua item dulu, sebelum ada yang disimpan
        foreach ($items as $item) {
            $partId = $item['sparepart_id'] ?? null;
            $jumlah = (int) ($item['jumlah'] ?? 0);
            if (!$partId || $jumlah <= 0) continue;

            $stock    = TechnicianStock::where('technician_id', $techId)
                ->where('sparepart_id', $partId)
                ->first();
            $sisaStok = $stock ? (int) $stock->jumlah : 0;

            if ($jumlah > $sisaStok) {
                $sparepart = Sparepart::find($partId);
                Notification::make()
                    ->title('Retur Gagal!')
                    ->body("Stok \"{$sparepart?->nama_sparepart}\" di tas teknisi tidak cukup. Sisa: {$sisaStok} pcs.")
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        $firstRecord = null;

        DB::transaction(function () use ($techId, $items, &$firstRecord) {
            foreach ($items as $item) {
                $partId = $item['sparepart_id'] ?? null;
                $jumlah = (int) ($item['jumlah'] ?? 0);
                if (!$partId || $jumlah <= 0) continue;

                // Simpan baris retur (tabel & struktur lama, tidak berubah)
                $record = PartReturn::create([
                    'technician_id' => $techId,
                    'sparepart_id'  => $partId,
                    'jumlah'        => $jumlah,
                ]);

                if (!$firstRecord) {
                    $firstRecord = $record;
                }

                // 1. Tambah stok Gudang Utama
                $sparepart = Sparepart::find($partId);
                if ($sparepart) {
                    $sparepart->increment('stok', $jumlah);
                }

                // 2. Kurangi dari Tas Teknisi
                $techStock = TechnicianStock::where('technician_id', $techId)
                    ->where('sparepart_id', $partId)
                    ->first();

                if ($techStock) {
                    $techStock->decrement('jumlah', $jumlah);
                    $techStock->refresh();
                    $saldoAkhirTeknisi = (int) $techStock->jumlah;
                } else {
                    $saldoAkhirTeknisi = 0;
                }

                // 3. Catat riwayat mutasi teknisi
                TechnicianStockHistory::create([
                    'technician_id' => $techId,
                    'sparepart_id'  => $partId,
                    'keluar'        => $jumlah,
                    'saldo_akhir'   => $saldoAkhirTeknisi,
                    'keterangan'    => 'Retur Part ke Gudang Utama',
                ]);
            }

            // 4. Notifikasi sukses (sekali untuk semua item)
            Notification::make()
                ->title('Retur Berhasil! ✅')
                ->body('Stok gudang bertambah, stok tas teknisi berkurang.')
                ->success()
                ->seconds(6)
                ->send();
        });

        $this->kirimNotifikasiWhatsapp($techId, $items);

        return $firstRecord ?? new PartReturn();
    }

    protected function kirimNotifikasiWhatsapp(int $technicianId, array $items): void
    {
        $technician = Technician::find($technicianId);

        if (!$technician || empty($technician->nomor_hp)) {
            return;
        }

        $daftarPart = collect($items)->map(function ($item) {
            $sparepart = Sparepart::find($item['sparepart_id'] ?? null);
            $jumlah    = $item['jumlah'] ?? 0;
            return $sparepart ? "- {$sparepart->nama_sparepart} x{$jumlah}" : null;
        })->filter()->implode("\n");

        $message = "Halo {$technician->nama_technician},\n\n"
            . "Retur sparepart berikut telah diproses ke Gudang Utama:\n"
            . "{$daftarPart}\n\n"
            . "Terima kasih.";

        try {
            Http::timeout(10)->post('http://localhost:3001/send', [
                'number'  => $technician->nomor_hp,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WA notifikasi retur: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}