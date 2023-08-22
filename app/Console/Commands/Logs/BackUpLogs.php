<?php

namespace App\Console\Commands\Logs;

use App\Domain\General\Services\BackupLogsService;
use Illuminate\Console\Command;

class BackUpLogs extends Command
{
    const URL_PATH = '';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the logs in the prod env to S3';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bp_log_service = new BackupLogsService();
        $bp_log_service->send(config('app.prod_url'), env('APP_PROD_INTERNAL_TOKEN'));
    }
}
