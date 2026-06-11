<?php
namespace App\Filament\Resources\CashMutationResource\Pages;
use App\Filament\Resources\CashMutationResource;
use App\Models\CashLedger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
class CreateCashMutation extends CreateRecord {
    protected static string $resource = CashMutationResource::class;
    protected function afterCreate(): void {
        $record = $this->record;
        $uraianList = $record->items->pluck('uraian')->implode(', ');
        CashLedger::create([
            'tanggal'     => $record->tanggal,
            'no_surat'    => $record->no_voucher,
            'keterangan'  => '[SPM] ' . $uraianList,
            'uang_masuk'  => 0,
            'uang_keluar' => $record->total_jumlah,
            'dibuat_oleh' => $record->pembuat,
        ]);
        $record->update(['sudah_dicatat_kas' => true]);
        Notification::make()->title('SPM berhasil disimpan dan otomatis dicatat sebagai Kas Keluar!')->success()->send();
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}
