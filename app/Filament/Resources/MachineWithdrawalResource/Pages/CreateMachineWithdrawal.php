<?php

namespace App\Filament\Resources\MachineWithdrawalResource\Pages;

use App\Filament\Resources\MachineWithdrawalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMachineWithdrawal extends CreateRecord
{
    protected static string $resource = MachineWithdrawalResource::class;

    protected function getRedirectUrl(): string
    {
        return route('cetak.surat-penarikan', $this->getRecord()->id);
    }
}
