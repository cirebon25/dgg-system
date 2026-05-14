<?php

namespace App\Filament\Resources\PartReturnResource\Pages;

use App\Filament\Resources\PartReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartReturn extends EditRecord
{
    protected static string $resource = PartReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
