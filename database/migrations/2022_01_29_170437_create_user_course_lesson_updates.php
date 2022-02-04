<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseLessonUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_lesson_updates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_course_lesson_id')->index()->unsigned();
            $table->integer('user_id')->index()->unsigned();
            $table->integer('progress')->index()->unsigned()->comment('the user progress in the lesson video, in percentages');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_course_lesson_updates');
    }
}
