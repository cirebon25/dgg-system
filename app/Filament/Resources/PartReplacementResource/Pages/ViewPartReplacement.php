<?php

namespace App\Filament\Resources\PartReplacementResource\Pages;

use App\Filament\Resources\PartReplacementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPartReplacement extends ViewRecord
{
    protected static string $resource = PartReplacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
