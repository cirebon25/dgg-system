<?php
namespace App\Filament\Resources\CashMutationResource\Pages;
use App\Filament\Resources\CashMutationResource;
use App\Models\CashLedger;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
class EditCashMutation extends EditRecord {
    protected static string $resource = CashMutationResource::class;
    protected function afterSave(): void {
        $record = $this->record;
        $uraianList = $record->items->pluck('uraian')->implode(', ');
        $ledger = CashLedger::where('no_surat', $record->no_voucher)->where('keterangan', 'like', '[SPM]%')->first();
        if ($ledger) {
            $ledger->update([
                'tanggal'     => $record->tanggal,
                'keterangan'  => '[SPM] ' . $uraianList,
                'uang_keluar' => $record->total_jumlah,
                'dibuat_oleh' => $record->pembuat,
            ]);
        }
        Notification::make()->title('Data SPM berhasil diperbarui!')->success()->send();
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}
