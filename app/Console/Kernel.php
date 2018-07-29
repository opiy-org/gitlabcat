<?php

namespace App\Console;

use App\Console\Commands\DomainCheck;
use App\Console\Commands\GetOpenTasks;
use App\Console\Commands\GetStats;
use App\Console\Commands\GetTgWebhook;
use App\Console\Commands\InstanceCheck;
use App\Console\Commands\BroadcastMessage;
use App\Console\Commands\RegTgWebhook;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DomainCheck::class,
        InstanceCheck::class,
        GetTgWebhook::class,
        RegTgWebhook::class,
        GetStats::class,
        GetOpenTasks::class,
        BroadcastMessage::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(DomainCheck::class)->daily();
        $schedule->command(InstanceCheck::class)->hourly();

//        $schedule->command(BroadcastMessage::class)->weekdays()->at('10:55');
//        $schedule->command(GetOpenTasks::class)->dailyAt('9:00');
//        $schedule->command(GetStats::class)->fridays()->at('18:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
