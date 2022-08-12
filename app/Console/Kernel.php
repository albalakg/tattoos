<?php

namespace App\Console;

use App\Domain\Content\Services\ContentService;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Domain\Users\Services\DisableExpiredUserCoursesService;

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

    private LogService  $log_service;

    private Schedule $schedule;

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->log_service  = new LogService('scheduler');
        $this->schedule     = $schedule;

        
        $this->checkForExpiredCourses();
    }

    /**
     * Runs once a day at 00:00
    */
    private function checkForExpiredCourses()
    {
        try {
            $this->schedule->call(function () {
                $service = new DisableExpiredUserCoursesService(new ContentService, new MailService);
                $service->handler();
                $this->log_service->info('checkForExpiredCourses ran successfully');            
            })->cron('0 0 * * *');
        } catch (\Exception $ex) {
            $this->log_service->error($ex);
        }
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
