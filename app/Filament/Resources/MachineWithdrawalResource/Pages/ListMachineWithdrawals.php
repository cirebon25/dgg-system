<?php

namespace App\Filament\Resources\MachineWithdrawalResource\Pages;

use App\Filament\Resources\MachineWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMachineWithdrawals extends ListRecords
{
    protected static string $resource = MachineWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
