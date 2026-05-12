<?php

namespace App\Observers;

use App\Models\MachineReplacement;
use App\Models\ServiceLog;
use App\Models\Machine;

class ReplacementObserver
{
    public function created(MachineReplacement $replacement): void
    {
        // 1. OTOMATIS INPUT KE SERVICE LOG
        ServiceLog::create([
            'machine_id' => $replacement->old_machine_id, 
            'customer_id' => $replacement->customer_id,
            'technician_id' => $replacement->technician_id,
            'tanggal' => $replacement->tanggal,
            'perbaikan' => "Tukar Guling: Unit ditarik & diganti dengan Unit Baru.",
        ]);

        // 2. MESIN LAMA: Ubah status jadi Ready (Masuk Gudang)
        Machine::where('id', $replacement->old_machine_id)->update([
            'status' => 'Ready',
        ]);

        // 3. MESIN BARU: Ubah status jadi Rented (Keluar ke Lokasi)
        Machine::where('id', $replacement->new_machine_id)->update([
            'status' => 'Rented',
        ]);
    }
}