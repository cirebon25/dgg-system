<?php

namespace App\Filament\Resources\CustomerRoResource\Pages;

use App\Filament\Resources\CustomerRoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerRo extends EditRecord
{
    protected static string $resource = CustomerRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
