<?php

namespace App\Console;

use App\Jobs\AppointmentReminderJob;
use App\Jobs\AutoRejectNoPaymentBookingJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->job(new AppointmentReminderJob)
             //->cron('* 0,20,30 * * *');   // fire every :00 or :30
//             ->everyTwoMinutes();
            ->everyThirtyMinutes();
         //
        $schedule->job(new AutoRejectNoPaymentBookingJob)->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
