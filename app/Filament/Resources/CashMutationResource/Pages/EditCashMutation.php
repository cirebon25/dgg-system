<?php

namespace App\Filament\Resources\CashMutationResource\Pages;

use App\Filament\Resources\CashMutationResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCashMutation extends EditRecord
{
    protected static string $resource = CashMutationResource::class;

    // afterSave() update CashLedger DIHAPUS -- sudah ditangani otomatis
    // oleh CashMutationObserver::updated(). Kalau dibiarkan di sini juga,
    // akan konflik dengan Observer dan menyebabkan data buku kas kacau.

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Data SPM berhasil diperbarui!')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
