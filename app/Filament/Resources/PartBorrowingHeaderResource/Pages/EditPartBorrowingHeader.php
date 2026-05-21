<?php

namespace App\Filament\Resources\PartBorrowingHeaderResource\Pages;

use App\Filament\Resources\PartBorrowingHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartBorrowingHeader extends EditRecord
{
    protected static string $resource = PartBorrowingHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
