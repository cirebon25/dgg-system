<?php
namespace App\Filament\Resources\CashReceiptResource\Pages;
use App\Filament\Resources\CashReceiptResource;
use App\Models\CashLedger;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
class EditCashReceipt extends EditRecord {
    protected static string $resource = CashReceiptResource::class;
    protected function afterSave(): void {
        $record = $this->record;
        $ledger = CashLedger::where('no_surat', $record->no_bukti)->where('keterangan', 'like', '[KAS MASUK]%')->first();
        if ($ledger) {
            $ledger->update([
                'tanggal'     => $record->tanggal,
                'no_surat'    => $record->no_bukti,
                'keterangan'  => '[KAS MASUK] ' . $record->sumber_dana . ' — ' . $record->keterangan,
                'uang_masuk'  => $record->jumlah,
                'dibuat_oleh' => $record->dibuat_oleh,
            ]);
        }
        Notification::make()->title('Data Kas Masuk berhasil diperbarui!')->success()->send();
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}
