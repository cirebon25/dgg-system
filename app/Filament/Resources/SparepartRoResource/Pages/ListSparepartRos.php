<?php

namespace App\Filament\Resources\SparepartRoResource\Pages;

use App\Filament\Resources\SparepartRoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSparepartRos extends ListRecords
{
    protected static string $resource = SparepartRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
