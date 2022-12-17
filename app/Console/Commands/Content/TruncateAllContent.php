<?php

namespace App\Console\Commands\Content;

use Exception;
use Illuminate\Console\Command;
use App\Domain\Content\Services\TermService;
use App\Domain\Content\Services\SkillService;
use App\Domain\Content\Services\VideoService;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Services\TrainerService;
use App\Domain\Content\Services\EquipmentService;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Services\CourseCategoryService;
use App\Domain\Content\Services\CourseRecommendationService;

class TruncateAllContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all the content in the application';

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
     * The order of the content list is important since they are connected
     * and must delete first the contents the other content depends on
     * For example: Cannot delete a video if a lesson is using it.
     *              so we must delete the videos first and afterwards the lessons
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure you want to delete all the content?')) {
            return;
        }

        try {
            $content_list = [
                new CourseLessonService,
                new CourseRecommendationService,
                new CourseAreaService(new CourseLessonService),
                new CourseService(new CourseAreaService),
                new CourseCategoryService(new CourseService),
                new VideoService(new CourseLessonService),
                new SkillService(new CourseLessonService),
                new TermService(new CourseLessonService),
                new EquipmentService(new CourseLessonService),
                new TrainerService(new CourseAreaService),
            ];
    
            $bar = $this->output->createProgressBar(count($content_list));
    
            $course_lesson_service = new CourseLessonService();
            $course_lesson_service->truncateAllLessonAssignedContent();
    
            foreach($content_list AS $content_service) {
                try {
                    $content_service->truncate();
                } catch(Exception $ex) {
                    $this->error($ex->__toString());
                }
                $bar->advance();
            }
    
            $this->info('');
            $this->info('Content has been fully deleted');
        } catch(Exception $ex) {
            $this->error($ex->__toString());
        }
    }
}
