<?php

namespace App\Filament\Resources\RayonResource\Pages;

use App\Filament\Resources\RayonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRayon extends EditRecord
{
    protected static string $resource = RayonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
