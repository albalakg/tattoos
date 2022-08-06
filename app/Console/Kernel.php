<?php

namespace App\Console;

use App\Domain\Helpers\LogService;
use App\Domain\Users\Services\UserService;
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

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->log_service  = new LogService('scheduler');

        $this->checkForExpiredCourses();
    }

    private function checkForExpiredCourses()
    {
        try {
            $service = new DisableExpiredUserCoursesService;
            $service->handler();
            $this->log_service->info('checkForExpiredCourses ran successfully');            
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
