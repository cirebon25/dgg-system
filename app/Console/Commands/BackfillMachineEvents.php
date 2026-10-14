<?php

namespace App\Console\Commands;

use App\Models\MachineEvent;
use App\Models\ServiceLog;
use App\Models\ServiceLogSparepart;
use Illuminate\Console\Command;

class BackfillMachineEvents extends Command
{
    protected $signature = 'machine-events:backfill';
    protected $description = 'Isi machine_events dari Service Log yang sudah ada';

    public function handle(): int
    {
        ServiceLog::chunkById(200, fn($c) => $c->each(fn($l) => MachineEvent::syncServiceLog($l)));
        ServiceLogSparepart::with('serviceLog')
            ->chunkById(200, fn($c) => $c->each(fn($i) => MachineEvent::syncPart($i)));

        $this->info('Selesai.');
        return self::SUCCESS;
    }
}