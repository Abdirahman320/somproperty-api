<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule): void {
        // Send overdue reminders every day at 9am
        $schedule->command('billing:send-overdue-reminders')->dailyAt('09:00');
        // Generate next month bills on 25th
        $schedule->command('billing:generate-monthly')->monthlyOn(25,'08:00');
        // Warn about contracts expiring in 30 days
        $schedule->command('contracts:expiry-warnings')->daily();
        // Auto-mark overdue bills
        $schedule->command('billing:mark-overdue')->dailyAt('00:05');
    }
    protected function commands(): void {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
