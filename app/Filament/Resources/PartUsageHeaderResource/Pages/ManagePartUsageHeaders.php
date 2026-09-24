<?php

namespace App\Filament\Resources\PartUsageHeaderResource\Pages;

use App\Filament\Resources\PartUsageHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePartUsageHeaders extends ManageRecords
{
    protected static string $resource = PartUsageHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
