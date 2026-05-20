<?php

namespace App\Filament\Resources\PartReturnResource\Pages;

use App\Filament\Resources\PartReturnResource;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePartReturn extends CreateRecord
{
    protected static string $resource = PartReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $techId = $data['technician_id'] ?? null;
        $partId = $data['sparepart_id']  ?? null;
        $jumlah = (int) ($data['jumlah'] ?? 0);

        // Validasi stok teknisi sebelum simpan
        if ($techId && $partId && $jumlah > 0) {
            $stock    = TechnicianStock::where('technician_id', $techId)
                            ->where('sparepart_id', $partId)
                            ->first();
            $sisaStok = $stock ? (int) $stock->jumlah : 0;

            if ($jumlah > $sisaStok) {
                Notification::make()
                    ->title('Retur Gagal!')
                    ->body("Stok di tas teknisi tidak cukup. Sisa saat ini: {$sisaStok} pcs.")
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
        $record = $this->getRecord();

        DB::transaction(function () use ($record) {
            $jumlah = (int) $record->jumlah;

            // 1. Tambah stok Gudang Utama
            $sparepart = Sparepart::find($record->sparepart_id);
            if ($sparepart) {
                $sparepart->increment('stok', $jumlah);
            }

            // 2. Kurangi dari Tas Teknisi
            $techStock = TechnicianStock::where('technician_id', $record->technician_id)
                ->where('sparepart_id', $record->sparepart_id)
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
                'technician_id' => $record->technician_id,
                'sparepart_id'  => $record->sparepart_id,
                'keluar'        => $jumlah,
                'saldo_akhir'   => $saldoAkhirTeknisi,
                'keterangan'    => 'Retur Part ke Gudang Utama',
            ]);

            // 4. Notifikasi sukses
            Notification::make()
                ->title('Retur Berhasil! ✅')
                ->body("Stok gudang bertambah {$jumlah} pcs. Sisa di tas teknisi: {$saldoAkhirTeknisi} pcs.")
                ->success()
                ->seconds(6)
                ->send();
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}