<?php

namespace App\Filament\Resources\RayonResource\Pages;

use App\Filament\Resources\RayonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRayons extends ListRecords
{
    protected static string $resource = RayonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
