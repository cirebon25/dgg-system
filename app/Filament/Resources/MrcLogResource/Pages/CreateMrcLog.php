<?php

namespace App\Filament\Resources\MrcLogResource\Pages;

use App\Filament\Resources\MrcLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMrcLog extends CreateRecord
{
    protected static string $resource = MrcLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Wajib true karena MrcLogResource::getEloquentQuery() hanya
        // menampilkan record dengan is_mrc = true.
        $data['is_mrc'] = true;

        // Field tipe_kunjungan tidak ditampilkan di form (khusus resource
        // ini), tapi kolomnya NOT NULL di database — set otomatis.
        $data['tipe_kunjungan'] = 'MRC';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Sinkronkan counter terbaru mesin dengan input yang baru saja
        // disimpan, supaya "Counter Lalu" berikutnya selalu akurat.
        if ($record->machine_id) {
            \App\Models\Machine::whereKey($record->machine_id)->update([
                'counter_bw'    => $record->counter_bw,
                'counter_color' => $record->counter_color,
            ]);
        }
    }
}
