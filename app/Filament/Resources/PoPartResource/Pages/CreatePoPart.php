<?php

namespace App\Filament\Resources\PoPartResource\Pages;

use App\Filament\Resources\PoPartResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePoPart extends CreateRecord
{
    protected static string $resource = PoPartResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        \Illuminate\Support\Facades\Log::info('POPARTE: handleRecordCreation dipanggil', [
            'data_keys' => array_keys($data),
            'items'     => $data['items'] ?? 'TIDAK ADA',
        ]);

        return parent::handleRecordCreation($data);
    }

    protected function afterCreate(): void
    {
        \Illuminate\Support\Facades\Log::info('POPARTE: afterCreate dipanggil', [
            'record_id'    => $this->record->id,
            'record_items' => $this->record->items->toArray(),
        ]);
    }
}
