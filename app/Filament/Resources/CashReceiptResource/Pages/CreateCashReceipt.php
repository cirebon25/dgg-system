<?php

namespace App\Filament\Resources\CashReceiptResource\Pages;

use App\Filament\Resources\CashReceiptResource;
use App\Models\CashLedger;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCashReceipt extends CreateRecord
{
    protected static string $resource = CashReceiptResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Guard: cegah double insert
        if (CashLedger::where('cash_receipt_id', $record->id)->exists()) {
            return;
        }

        CashLedger::create([
            'cash_receipt_id' => $record->id,
            'tanggal'         => $record->tanggal,
            'no_surat'        => $record->no_bukti,
            'keterangan'      => '[KAS MASUK] ' . $record->sumber_dana . ' — ' . $record->keterangan,
            'uang_masuk'      => $record->jumlah,
            'uang_keluar'     => 0,
            'dibuat_oleh'     => $record->dibuat_oleh,
        ]);

        Notification::make()->title('Kas Masuk berhasil disimpan dan dicatat di Buku Kas!')->success()->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
