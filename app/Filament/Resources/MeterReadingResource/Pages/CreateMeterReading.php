<?php

namespace App\Filament\Resources\MeterReadingResource\Pages;

use App\Filament\Resources\MeterReadingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMeterReading extends CreateRecord
{
    protected static string $resource = MeterReadingResource::class;

    protected function afterCreate(): void
    {
        $r = $this->record;

        if ($r->machine_id) {
            $update = ['counter_bw' => $r->counter_bw];

            if ($r->counter_color !== null) {
                $update['counter_color'] = $r->counter_color;
            }

            \App\Models\Machine::whereKey($r->machine_id)->update($update);
        }
    }
}
