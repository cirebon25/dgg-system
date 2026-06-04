<?php

namespace App\Filament\Resources\MachineReturnResource\Pages;

use App\Filament\Resources\MachineReturnResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMachineReturn extends CreateRecord
{
    protected static string $resource = MachineReturnResource::class;

    // Setelah submit, langsung redirect ke halaman cetak surat jalan
    protected function getRedirectUrl(): string
    {
        return route('cetak.surat-retur', $this->record->id);
    }

    protected function afterCreate(): void
    {
        $sn = $this->record->machine->serial_number ?? '-';
        Notification::make()
            ->title("Mesin {$sn} dicatat dikirim ke Bandung. Mencetak surat jalan...")
            ->success()
            ->send();
    }
}