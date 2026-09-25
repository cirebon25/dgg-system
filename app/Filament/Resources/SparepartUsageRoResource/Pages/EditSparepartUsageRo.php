<?php

namespace App\Filament\Resources\SparepartUsageRoResource\Pages;

use App\Filament\Resources\SparepartUsageRoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartUsageRo extends EditRecord
{
    protected static string $resource = SparepartUsageRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
