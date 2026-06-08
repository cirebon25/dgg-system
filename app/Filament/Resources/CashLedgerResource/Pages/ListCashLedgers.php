<?php

namespace App\Filament\Resources\CashLedgerResource\Pages;

use App\Filament\Resources\CashLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashLedgers extends ListRecords
{
    protected static string $resource = CashLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
