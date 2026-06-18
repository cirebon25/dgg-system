<?php

namespace App\Filament\Resources\ServiceLogResource\Pages;

use App\Filament\Resources\ServiceLogResource;
use App\Models\ServiceLog;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditServiceLog extends EditRecord
{
    protected static string $resource = ServiceLogResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['bw_lalu'], $data['color_lalu']);
        return $data;

       $lastLog = \App\Models\ServiceLog::where('machine_id', $data['machine_id'])
        ->where('id', '!=', $this->record->id)
        ->latest('id')
        ->first();

    $bwLalu = (int) ($lastLog?->counter_bw ?? 0);

    if ((int) ($data['counter_bw'] ?? 0) < $bwLalu) {
        \Filament\Notifications\Notification::make()
            ->title('Counter BW tidak valid!')
            ->body("Counter BW sekarang tidak boleh kurang dari counter lalu ({$bwLalu}).")
            ->danger()
            ->send();
        $this->halt();
    }

    unset($data['bw_lalu'], $data['color_lalu']);
    return $data;
    }

    protected function afterSave(): void
    {
        $record    = $this->record;
        $machineId = $record->machine_id;

        $lastLog = ServiceLog::where('machine_id', $machineId)
            ->where('id', '!=', $record->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $bwLalu    = $lastLog?->counter_bw    ?? 0;
        $colorLalu = $lastLog?->counter_color ?? 0;

        DB::table('service_logs')
            ->where('id', $record->id)
            ->update([
                'usage_bw'    => max(0, (int)$record->counter_bw    - (int)$bwLalu),
                'usage_color' => max(0, (int)$record->counter_color - (int)$colorLalu),
            ]);
    }
   
}