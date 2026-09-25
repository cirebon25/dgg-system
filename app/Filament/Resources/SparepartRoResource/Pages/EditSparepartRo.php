<?php

namespace App\Filament\Resources\SparepartRoResource\Pages;

use App\Filament\Resources\SparepartRoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartRo extends EditRecord
{
    protected static string $resource = SparepartRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
