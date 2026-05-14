<?php

namespace App\Filament\Resources\SparepartEntryResource\Pages;

use App\Filament\Resources\SparepartEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSparepartEntries extends ListRecords
{
    protected static string $resource = SparepartEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
