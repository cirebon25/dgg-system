<?php

namespace App\Filament\Resources\MachineReturnResource\Pages;

use App\Filament\Resources\MachineReturnResource;
use App\Models\MachineReturn;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreateMachineReturn extends CreateRecord
{
    protected static string $resource = MachineReturnResource::class;

    private string $batchId = '';

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $machineList = $data['machine_list'] ?? [];
        unset($data['machine_list']);

        if (empty($machineList)) {
            Notification::make()->title('Pilih minimal 1 mesin!')->danger()->send();
            throw new \Exception('Tidak ada mesin yang dipilih.');
        }

        // Generate 1 batch_id untuk semua mesin dalam sesi ini
        $this->batchId = (string) Str::uuid();

        $firstRecord = null;

        foreach ($machineList as $item) {
            $record = MachineReturn::create(array_merge($data, [
                'machine_id' => $item['machine_id'],
                'batch_id'   => $this->batchId,
            ]));

            if (!$firstRecord) {
                $firstRecord = $record;
            }
        }

        $count = count($machineList);
        Notification::make()
            ->title("{$count} mesin berhasil diretur ke Bandung!")
            ->success()
            ->send();

        return $firstRecord;
    }

    // ✅ Redirect ke halaman cetak setelah create
    protected function getRedirectUrl(): string
    {
        return route('cetak.surat-retur-batch', $this->batchId);
    }
}