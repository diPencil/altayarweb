<?php

namespace App\Console;

use App\Models\UserMembership;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            UserMembership::where('status', 1)
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', now())
                ->update(['status' => 2]);
        })->daily();

        // Sync airline logos from the assets folder into public so they are web-accessible.
        // Runs every 5 minutes; adjust as needed.
        $schedule->command('sync:airline-logos')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
