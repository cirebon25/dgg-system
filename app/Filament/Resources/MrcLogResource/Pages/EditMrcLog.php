<?php

namespace App\Filament\Resources\MrcLogResource\Pages;

use App\Filament\Resources\MrcLogResource;
use Filament\Resources\Pages\EditRecord;

class EditMrcLog extends EditRecord
{
    protected static string $resource = MrcLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
