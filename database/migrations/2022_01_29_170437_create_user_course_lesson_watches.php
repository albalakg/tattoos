<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseLessonWatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_lesson_watches', function (Blueprint $table) {
            $table->id();
            $table->integer('user_course_lesson_id')->index()->unsigned();
            $table->integer('course_lesson_id')->index()->unsigned();
            $table->integer('user_id')->index()->unsigned();
            $table->decimal('start_time')->index()->unsigned()->comment('the video time the user started to watch');
            $table->decimal('end_time')->index()->unsigned()->comment('the video time the user ended to watch');
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
        Schema::dropIfExists('user_course_lesson_watches');
    }
}
