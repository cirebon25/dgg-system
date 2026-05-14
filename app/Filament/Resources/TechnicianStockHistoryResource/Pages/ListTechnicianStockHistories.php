<?php

namespace App\Filament\Resources\TechnicianStockHistoryResource\Pages;

use App\Filament\Resources\TechnicianStockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechnicianStockHistories extends ListRecords
{
    protected static string $resource = TechnicianStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
