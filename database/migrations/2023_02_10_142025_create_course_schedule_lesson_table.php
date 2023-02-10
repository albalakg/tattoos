<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseScheduleLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_schedule_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('course_schedule_id')->index();
            $table->unsignedInteger('course_id')->index();
            $table->unsignedInteger('course_lesson_id')->index();
            $table->dateTime('date');
            $table->dateTime('created_at');
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
        Schema::dropIfExists('course_schedule_lessons');
    }
}
