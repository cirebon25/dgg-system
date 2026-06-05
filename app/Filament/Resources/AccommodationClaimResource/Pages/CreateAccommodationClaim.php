<?php

namespace App\Filament\Resources\AccommodationClaimResource\Pages;

use App\Filament\Resources\AccommodationClaimResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccommodationClaim extends CreateRecord
{
    protected static string $resource = AccommodationClaimResource::class;

    // Setelah simpan, langsung redirect ke halaman cetak
    protected function getRedirectUrl(): string
    {
        return route('cetak.klaim-akomodasi', $this->record->id);
    }
}
