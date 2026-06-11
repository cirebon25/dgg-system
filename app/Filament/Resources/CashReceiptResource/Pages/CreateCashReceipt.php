<?php
namespace App\Filament\Resources\CashReceiptResource\Pages;
use App\Filament\Resources\CashReceiptResource;
use App\Models\CashLedger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
class CreateCashReceipt extends CreateRecord {
    protected static string $resource = CashReceiptResource::class;
    protected function afterCreate(): void {
        $record = $this->record;
        CashLedger::create([
            'tanggal'     => $record->tanggal,
            'no_surat'    => $record->no_bukti,
            'keterangan'  => '[KAS MASUK] ' . $record->sumber_dana . ' — ' . $record->keterangan,
            'uang_masuk'  => $record->jumlah,
            'uang_keluar' => 0,
            'dibuat_oleh' => $record->dibuat_oleh,
        ]);
        Notification::make()->title('Kas Masuk berhasil disimpan dan dicatat di Buku Kas!')->success()->send();
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}
