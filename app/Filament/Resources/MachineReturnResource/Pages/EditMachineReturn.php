<?php

namespace App\Filament\Resources\MachineReturnResource\Pages;

use App\Filament\Resources\MachineReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMachineReturn extends EditRecord
{
    protected static string $resource = MachineReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
