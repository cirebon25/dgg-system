<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Concerns\WarnsOnDuplicate;
use App\Filament\Resources\DeploymentResource;
use App\Models\Deployment;
use Filament\Resources\Pages\CreateRecord;

class CreateDeployment extends CreateRecord
{
    use WarnsOnDuplicate;

    protected static string $resource = DeploymentResource::class;

    protected function findDuplicateRecord(array $data): ?Deployment
    {
        return Deployment::query()
            ->where('customer_id', $data['customer_id'] ?? null)
            ->where('machine_id', $data['machine_id'] ?? null)
            ->whereDate('tanggal_instal', $data['tanggal_instal'] ?? null)
            ->where('counter_bw', $data['counter_bw'] ?? null)
            ->where('counter_color', $data['counter_color'] ?? null)
            ->latest()
            ->first();
    }

    // FUNGSI SAKTI: Begitu sukses simpan, langsung lempar ke URL Cetak
    protected function getRedirectUrl(): string
    {
        return route('cetak.surat-jalan', ['id' => $this->record->id]);
    }
}
