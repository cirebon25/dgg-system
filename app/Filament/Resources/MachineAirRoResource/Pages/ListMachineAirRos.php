<?php

namespace App\Filament\Resources\MachineAirRoResource\Pages;

use App\Filament\Resources\MachineAirRoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMachineAirRos extends ListRecords
{
    protected static string $resource = MachineAirRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
