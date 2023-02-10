<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseScheduleLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_schedule_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('course_schedule_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('course_lesson_id')->index();
            $table->dateTime('date');
            $table->dateTime('created_at');
            $table->unsignedInteger('created_by')->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_course_schedule_lessons');
    }
}
