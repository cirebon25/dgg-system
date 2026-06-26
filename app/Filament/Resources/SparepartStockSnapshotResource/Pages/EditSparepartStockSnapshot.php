<?php

namespace App\Filament\Resources\SparepartStockSnapshotResource\Pages;

use App\Filament\Resources\SparepartStockSnapshotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartStockSnapshot extends EditRecord
{
    protected static string $resource = SparepartStockSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
