<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan schedule command di sini
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan arsip otomatis tiap hari
        $schedule->command('sales:archive-old 2')->dailyAt('09:00');
    }

    /**
     * Daftarkan command artisan kustom
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
