<?php

namespace App\Filament\Resources\AccommodationClaimResource\Pages;

use App\Filament\Resources\AccommodationClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccommodationClaim extends EditRecord
{
    protected static string $resource = AccommodationClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
