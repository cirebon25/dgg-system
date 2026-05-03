<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Resources\DeploymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeployment extends CreateRecord
{
    protected static string $resource = DeploymentResource::class;

    // FUNGSI SAKTI: Begitu sukses simpan, langsung lempar ke URL Cetak
    protected function getRedirectUrl(): string
    {
        return route('cetak.surat-jalan', ['id' => $this->record->id]);
    }
}
