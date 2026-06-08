<?php

namespace App\Filament\Resources\CashMutationResource\Pages;

use App\Filament\Resources\CashMutationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashMutation extends EditRecord
{
    protected static string $resource = CashMutationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
