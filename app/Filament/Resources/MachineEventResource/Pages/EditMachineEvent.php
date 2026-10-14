<?php

namespace App\Filament\Resources\MachineEventResource\Pages;

use App\Filament\Resources\MachineEventResource;
use Filament\Resources\Pages\EditRecord;

class EditMachineEvent extends EditRecord
{
    protected static string $resource = MachineEventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}