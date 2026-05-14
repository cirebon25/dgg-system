<?php

namespace App\Filament\Resources\PartBorrowingResource\Pages;

use App\Filament\Resources\PartBorrowingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartBorrowings extends ListRecords
{
    protected static string $resource = PartBorrowingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
