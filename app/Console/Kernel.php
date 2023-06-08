<?php

namespace App\Console;

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
        $vDay = date('Ymd');

        $schedule->command('EWO:cretePDF')
            ->description('EWO 自動產出PDF')
            ->dailyAt('03:05')
//            ->everyMinute()
            ->runInBackground()
            ->appendOutputTo(storage_path("logs/schedule/EWO_cretePDF_$vDay.log"));
        $schedule->command('EWO:clearEWOFolder')
            ->description('EWO 清除合併圖片的目錄[EWO_Folder]')
            ->dailyAt('01:05')
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/schedule/EWO_clearEWOFolder.log'));
        $schedule->command('EWO:checkCusetID')
            ->description('EWO 檢查[住編]改變工單[checkCusetID]')
            ->dailyAt('01:00')
            ->runInBackground()
            ->appendOutputTo(storage_path("logs/schedule/EWO_checkCusetID_$vDay.log"));
        $schedule->command('EWO:creatCache01')
            ->description('EWO 生成cache資料 維修 alertInfo')
            ->dailyAt('02:00')
            ->runInBackground()
            ->appendOutputTo(storage_path("logs/schedule/EWO_creatCache_$vDay.log"));

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
