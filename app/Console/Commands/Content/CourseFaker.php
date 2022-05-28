<?php

namespace App\Console\Commands\Content;

use Exception;
use Illuminate\Console\Command;
use App\Domain\Content\Services\VideoService;
use App\Domain\Content\Services\CourseService;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Content\Services\CourseCategoryService;
use App\Domain\Content\Services\GenerateCourseService;
use App\Domain\Content\Services\TrainerService;

class CourseFaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:generate {total_course_areas?} {total_lessons?} {total_trainers?} {total_video?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a fake course with course areas and lessons';

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
        try {
            $course_generator = new GenerateCourseService(
                new CourseService,
                new CourseAreaService,
                new CourseLessonService(new CourseAreaService),
                new CourseCategoryService,
                new VideoService,
                new TrainerService,
            );

            $course_generator->generate();

            if($course_generator->hasErrors()) {
                $errors = $course_generator->getErrors();
                foreach($errors AS $error) {
                    $this->error($error);
                }

                $this->info('');
                $this->error('A total of ' . count($errors) . ' errors');
            }

            $this->info('Generated the course successfully with the following details: ' . $course_generator->getGenerationDetails());
        } catch(Exception $ex) {
            $this->error('Failed generating the course: ' . $ex->getMessage());
        }
    }
}
