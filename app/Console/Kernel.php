<?php

namespace App\Console;

use App\Http\Controllers\Ajax\ImportacaoController;
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
        \App\Console\Commands\ExportDatabaseBackup::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Exporta o banco de dados inteiro para um arquivo .sql com CREATE TABLE e INSERT INTO
        $schedule->command('backup:multi-sql')->dailyAt('02:00')->withoutOverlapping()->runInBackground()->sendOutputTo(storage_path('logs/backup.log'))->before(function () {
            ini_set('memory_limit', '2048M');
        });

        $schedule->command('bi:converter-json')->everyFiveMinutes();

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
