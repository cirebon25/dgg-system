<?php

namespace App\Filament\Resources\PoPartResource\Pages;

use App\Filament\Resources\PoPartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoParts extends ListRecords
{
    protected static string $resource = PoPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
