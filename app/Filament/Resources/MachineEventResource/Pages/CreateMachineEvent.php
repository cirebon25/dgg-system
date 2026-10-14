<?php

namespace App\Filament\Resources\MachineEventResource\Pages;

use App\Filament\Resources\MachineEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMachineEvent extends CreateRecord
{
    protected static string $resource = MachineEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}