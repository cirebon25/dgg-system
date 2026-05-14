<?php

namespace App\Filament\Resources\SparepartEntryResource\Pages;

use App\Filament\Resources\SparepartEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartEntry extends EditRecord
{
    protected static string $resource = SparepartEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
