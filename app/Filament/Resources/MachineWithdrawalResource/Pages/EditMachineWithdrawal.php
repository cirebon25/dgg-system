<?php

namespace App\Filament\Resources\MachineWithdrawalResource\Pages;

use App\Filament\Resources\MachineWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMachineWithdrawal extends EditRecord
{
    protected static string $resource = MachineWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
