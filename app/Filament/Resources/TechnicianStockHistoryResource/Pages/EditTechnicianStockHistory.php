<?php

namespace App\Filament\Resources\TechnicianStockHistoryResource\Pages;

use App\Filament\Resources\TechnicianStockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTechnicianStockHistory extends EditRecord
{
    protected static string $resource = TechnicianStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
