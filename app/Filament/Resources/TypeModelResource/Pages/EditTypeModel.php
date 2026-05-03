<?php

namespace App\Filament\Resources\TypeModelResource\Pages;

use App\Filament\Resources\TypeModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeModel extends EditRecord
{
    protected static string $resource = TypeModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
