<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\FetchNewsJob;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new \App\Jobs\FetchNewsJob)
                 ->everyMinute()  // testing only – change to hourly later
                 ->withoutOverlapping(10)
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/news-fetch.log'))
                 ->emailOnFailure('your-email@example.com');


                 
    }
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
