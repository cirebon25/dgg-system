<?php

namespace App\Filament\Resources\TechnicianStockResource\Pages;

use App\Filament\Resources\TechnicianStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechnicianStocks extends ListRecords
{
    protected static string $resource = TechnicianStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
