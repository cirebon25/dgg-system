<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages;

use App\Filament\Resources\PartBorrowingHeaderResource;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePartBorrowingHeader extends CreateRecord
{
    protected static string $resource = PartBorrowingHeaderResource::class;

    private bool $sudahDiproses = false;

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
        if ($this->sudahDiproses) return;
        $this->sudahDiproses = true;

        $header = $this->getRecord();
        $header->load('items');

        DB::transaction(function () use ($header) {
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
                    'saldo_akhir'   => (int) $techStock->jumlah,
                    'keterangan'    => 'Pinjam Part dari Gudang Utama',
                ]);
            }
        });

        // 4. Cetak nota otomatis
        try {
            $printUrl = route('cetak.bukti-pinjam-multi', $header->id);
            $this->js("
                const win = window.open('{$printUrl}', '_blank');
                if (!win || win.closed || typeof win.closed == 'undefined') {
                    window.location.href = '{$printUrl}';
                }
            ");
        } catch (\Exception $e) {
            Log::error('Gagal cetak otomatis: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}