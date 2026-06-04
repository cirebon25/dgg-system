<?php

namespace App\Filament\Resources\MachineReturnResource\Pages;

use App\Filament\Resources\MachineReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMachineReturns extends ListRecords
{
    protected static string $resource = MachineReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Buat Retur Mesin'),
        ];
    }
}
