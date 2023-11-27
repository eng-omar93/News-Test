<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\ImportNews;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        ImportNews::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('import:news')
            // ->everyMinute(); //for testing   
            ->runInBackground() //If you have long-running tasks, this may cause subsequent tasks to start much later than anticipated.
            ->everyFifteenMinutes()
            ->name('import_news_job') //scheduled event name is required to prevent overlapping
            ->withoutOverlapping() // By default, scheduled tasks will be run even if the previous instance of the task is still running. To prevent this, you may use the withoutOverlapping method
            ->sendOutputTo(public_path('import_news_job_'. date('Y_m_d_H_i') .'.txt'));
            //for testing: php artisan schedule:run  -- without ->runInBackground()
            //to start: php artisan schedule:work
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
