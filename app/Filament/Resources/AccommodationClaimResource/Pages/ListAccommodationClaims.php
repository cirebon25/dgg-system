<?php

namespace App\Filament\Resources\AccommodationClaimResource\Pages;

use App\Filament\Resources\AccommodationClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccommodationClaims extends ListRecords
{
    protected static string $resource = AccommodationClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Buat Klaim Baru'),
        ];
    }
}
