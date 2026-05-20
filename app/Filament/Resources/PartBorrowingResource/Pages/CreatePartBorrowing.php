<?php

namespace App\Filament\Resources\PartBorrowingResource\Pages;

use App\Filament\Resources\PartBorrowingResource;
use App\Models\Sparepart;
use App\Models\TechnicianStock;
use App\Models\TechnicianStockHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePartBorrowing extends CreateRecord
{
    protected static string $resource = PartBorrowingResource::class;

    private bool $sudahDiproses = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
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
        if ($this->sudahDiproses) return;
        $this->sudahDiproses = true;

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
                'saldo_akhir'   => (int) $techStock->jumlah,
                'keterangan'    => 'Pinjam Part dari Gudang Utama',
            ]);
        });

        // 4. Cetak: simpan ID ke session lalu redirect ke halaman cetak
        // Cara ini 100% lolos blokir popup browser
        try {
            $printUrl = route('cetak.bukti-pinjam', $record->id);
            // Inject JS: buka cetak di tab yang sama lalu kembali ke list
            $this->js("
                const printUrl = '{$printUrl}';
                const listUrl  = window.location.origin + '/admin/part-borrowings';
                const win = window.open(printUrl, '_blank');
                if (!win || win.closed || typeof win.closed == 'undefined') {
                    // Popup diblokir browser — redirect langsung
                    window.location.href = printUrl;
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