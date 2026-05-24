<?php

namespace App\Filament\Resources\PartBorrowingResource\Pages;

use App\Filament\Resources\PartBorrowingResource;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePartBorrowing extends CreateRecord
{
    protected static string $resource = PartBorrowingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data); // SEMENTARA UNTUK DEBUG

        $jumlah = (int) ($data['jumlah'] ?? 0);
        $partId = $data['sparepart_id'] ?? null;

        if ($partId && $jumlah > 0) {
            $sparepart  = Sparepart::find($partId);
            $stokGudang = $sparepart ? (int) $sparepart->stok : 0;

            if ($jumlah > $stokGudang) {
                Notification::make()
                    ->title('Pinjam Gagal!')
                    ->body("Stok gudang tidak cukup. Sisa saat ini: {$stokGudang} pcs.")
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

            // 1. Potong stok Gudang Utama
            $sparepart = Sparepart::find($record->sparepart_id);
            if ($sparepart) {
                $sparepart->decrement('stok', $jumlah);
            }

            // 2. Tambah ke Tas Teknisi
            $techStock = TechnicianStock::firstOrCreate(
                [
                    'technician_id' => $record->technician_id,
                    'sparepart_id'  => $record->sparepart_id,
                ],
                ['jumlah' => 0]
            );
            $techStock->increment('jumlah', $jumlah);
            $techStock->refresh();

            // 3. Catat riwayat mutasi
            TechnicianStockHistory::create([
                'technician_id' => $record->technician_id,
                'sparepart_id'  => $record->sparepart_id,
                'masuk'         => $jumlah,
                'keluar'        => 0,
                'saldo_akhir'   => (int) $techStock->jumlah,
                'keterangan'    => 'Pinjam Part dari Gudang Utama',
            ]);
        });

        Notification::make()
            ->title('Pinjam Berhasil! ✅')
            ->body("Stok teknisi bertambah. Klik tombol Cetak di tabel untuk cetak bukti.")
            ->success()
            ->seconds(6)
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
