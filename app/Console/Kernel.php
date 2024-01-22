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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /*$schedule->call(function () {
            ImportacaoController::ImportaMSAccessCapitaniMysql();
        })->weekdays()->hourly()->between('7:00', '18:00');*/

        
        //})->weekdays()->everyMinute()->between('7:00', '18:00')
        //->emailOutputTo('jvgs_o.o@live.com');


		$schedule->call(function () {
            echo ImportacaoController::verificaNomeBoletim();
        //})->weekdays()->hourly()->between('7:00', '18:00')
        })->weekdays()
		->hourly()
		//->everyMinute()
		//->everyFiveMinutes()
		->between('16:00', '19:00');
		
        //$schedule->command('App\Http\Controllers\Ajax\ImportacaoController@verificaNomeBoletim')
		/*$schedule->call(function () {
			ImportacaoController::verificaNomeBoletim();
		})->weekdays()->everyMinute()->between('7:00', '18:00');*/
        //})->weekdays()->hourly()->between('7:00', '18:00')
        
		//->name("Citado em Boletim Interno")
		//->sendOutputTo(storage_path('logs/teste.log'))
        //->emailOutputTo('jvgs_o.o@live.com');

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
