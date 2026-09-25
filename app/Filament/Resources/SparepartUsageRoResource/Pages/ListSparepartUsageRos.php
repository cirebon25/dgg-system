<?php

namespace App\Filament\Resources\SparepartUsageRoResource\Pages;

use App\Filament\Resources\SparepartUsageRoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSparepartUsageRos extends ListRecords
{
    protected static string $resource = SparepartUsageRoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
