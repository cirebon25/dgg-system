<?php

namespace App\Filament\Resources\PartReturnResource\Pages;

use App\Filament\Resources\PartReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartReturns extends ListRecords
{
    protected static string $resource = PartReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
