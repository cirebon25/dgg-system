<?php

namespace App\Filament\Resources\CashMutationResource\Pages;

use App\Filament\Resources\CashMutationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashMutations extends ListRecords
{
    protected static string $resource = CashMutationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
