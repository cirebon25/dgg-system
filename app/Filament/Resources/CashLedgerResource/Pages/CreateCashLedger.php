<?php

namespace App\Filament\Resources\CashLedgerResource\Pages;

use App\Filament\Resources\CashLedgerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCashLedger extends CreateRecord
{
    protected static string $resource = CashLedgerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
