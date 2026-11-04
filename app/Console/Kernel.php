<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('sparepart:snapshot')
            ->monthlyOn(1, '00:00')
            ->name('snapshot-sparepart-stock')
            ->withoutOverlapping();

        // Dijalankan di proses yang sama (tanpa membuka php.exe baru),
        // supaya tidak ada jendela terminal yang muncul tiap menit.
        $schedule->call(fn() => Artisan::call('fingerspot:sync'))
            ->name('sync-fingerspot-absensi')
            ->everyMinute()
            ->withoutOverlapping(10);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}