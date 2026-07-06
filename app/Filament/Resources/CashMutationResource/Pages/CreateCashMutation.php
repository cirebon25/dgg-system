<?php

namespace App\Filament\Resources\CashMutationResource\Pages;

use App\Filament\Resources\CashMutationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCashMutation extends CreateRecord
{
    protected static string $resource = CashMutationResource::class;

    // afterCreate() DIHAPUS -- pencatatan CashLedger sudah ditangani
    // oleh CashMutationObserver::created() secara otomatis. Kalau dibiarkan
    // di sini juga, kas akan terpotong 2x untuk setiap 1 input SPM.

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('SPM berhasil disimpan dan otomatis dicatat sebagai Kas Keluar!')
            ->success();
    }
}
