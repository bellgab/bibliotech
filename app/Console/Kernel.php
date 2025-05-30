<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send due reminders every day at 9:00 AM
        $schedule->command('send:borrow-reminders due')
                 ->dailyAt('09:00')
                 ->name('daily-due-reminders')
                 ->withoutOverlapping()
                 ->onOneServer();

        // Send overdue reminders every day at 10:00 AM
        $schedule->command('send:borrow-reminders overdue')
                 ->dailyAt('10:00')
                 ->name('daily-overdue-reminders')
                 ->withoutOverlapping()
                 ->onOneServer();

        // Send early reminders (3 days before due) every Monday at 8:00 AM
        $schedule->command('send:borrow-reminders early')
                 ->weeklyOn(1, '08:00')
                 ->name('weekly-early-reminders')
                 ->withoutOverlapping()
                 ->onOneServer();
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
