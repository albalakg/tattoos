<?php

namespace App\Console\Commands\Content;

use Illuminate\Console\Command;
use App\Domain\Helpers\FileService;
use Illuminate\Support\Facades\Storage;
use App\Domain\Content\Models\CourseLesson;
use Exception;

class MoveLessonsImagesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:moveLessonsImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move lessons\' images to S3';

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
        $lessons = CourseLesson::all();
        foreach($lessons AS $lesson) {
            try {
                $file_path = Storage::disk('pub')->path($lesson->image);
                Storage::disk(FileService::S3_DISK)->put($lesson->image, file_get_contents($file_path));
                $this->info('Finished moving lesson: ' . $lesson->name);
            } catch(Exception $ex) {
                $this->error('Failed moving the file: ' . $lesson->name);
                $this->error('Error: ' . $ex->getMessage());
            }
        }
        $this->info('Finished moving all ' . count($lessons) . ' lessons');
    }
}
