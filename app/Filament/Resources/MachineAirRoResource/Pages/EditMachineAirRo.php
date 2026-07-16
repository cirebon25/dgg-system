<?php

namespace App\Filament\Resources\MachineAirRoResource\Pages;

use App\Filament\Resources\MachineAirRoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMachineAirRo extends EditRecord
{
    protected static string $resource = MachineAirRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
