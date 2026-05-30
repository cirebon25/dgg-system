<?php

namespace App\Filament\Resources\PartReplacementResource\Pages;

use App\Filament\Resources\PartReplacementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartReplacement extends EditRecord
{
    protected static string $resource = PartReplacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
