<?php

namespace App\Filament\Resources\SparepartEntryResource\Pages;

use App\Filament\Resources\SparepartEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSparepartEntry extends CreateRecord
{
    protected static string $resource = SparepartEntryResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $items    = $data['items_masuk'] ?? [];
        $firstRecord = null;

        foreach ($items as $index => $item) {
            $newRecord = static::getModel()::create([
                'sparepart_id' => $item['sparepart_id'],
                'jumlah'       => $item['jumlah'],
                'supplier'     => $data['supplier'] ?? null,
                'keterangan'   => $data['keterangan'] ?? null,
            ]);

            // Stok fisik ditambah via booted() di model SparepartEntry
            // Tidak perlu kalkulasi manual di sini

            if ($index === 0) {
                $firstRecord = $newRecord;
            }
        }

        return $firstRecord ?? static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
