<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('user_course_id')->unsigned()->index();
            $table->integer('course_lesson_id')->unsigned()->index();
            $table->integer('status')->unsigned()->index();
            $table->dateTime('created_at');
            $table->dateTime('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_course_lessons');
    }
}
