<?php

namespace App\Filament\Resources\CashLedgerResource\Pages;

use App\Filament\Resources\CashLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashLedger extends EditRecord
{
    protected static string $resource = CashLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
