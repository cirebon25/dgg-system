<?php

namespace App\Filament\Resources\PartUsageHeaderResource\Pages;

use App\Filament\Resources\PartUsageHeaderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartUsageHeader extends CreateRecord
{
    protected static string $resource = PartUsageHeaderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
