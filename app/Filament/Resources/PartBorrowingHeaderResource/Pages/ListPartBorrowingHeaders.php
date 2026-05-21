<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages;

use App\Filament\Resources\PartBorrowingHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartBorrowingHeaders extends ListRecords
{
    protected static string $resource = PartBorrowingHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Pinjam Part'),
        ];
    }
}