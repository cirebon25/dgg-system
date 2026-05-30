<?php

namespace App\Filament\Resources\ServiceLogResource\Pages;

use App\Filament\Resources\ServiceLogResource;
use App\Models\ServiceLog;
use App\Models\PartReplacement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateServiceLog extends CreateRecord
{
    protected static string $resource = ServiceLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['bw_lalu'], $data['color_lalu']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $record    = $this->record;
        $machineId = $record->machine_id;

        // === Hitung usage_bw & usage_color ===
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

        // === Simpan riwayat ganti part ===
        foreach ($record->serviceLogSpareparts as $item) {
            $sparepartId = $item->sparepart_id;

            $lastReplacement = PartReplacement::where('machine_id', $machineId)
                ->where('sparepart_id', $sparepartId)
                ->latest('id')
                ->first();

            $counterSebelumnya = $lastReplacement?->counter_saat_ganti ?? 0;
            $counterSekarang   = (int)$record->counter_bw;
            $selisih           = max(0, $counterSekarang - $counterSebelumnya);

            PartReplacement::create([
                'machine_id'         => $machineId,
                'sparepart_id'       => $sparepartId,
                'service_log_id'     => $record->id,
                'counter_saat_ganti' => $counterSekarang,
                'counter_sebelumnya' => $counterSebelumnya,
                'selisih'            => $selisih,
                'tanggal'            => $record->tanggal,
            ]);
        }
    }
}
