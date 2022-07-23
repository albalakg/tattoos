<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lesson_details', function (Blueprint $table) {
            $table->integer('course_lesson_id')->unsigned()->index();
            $table->integer('video_id')->unsigned()->index();
            $table->text('description');
            $table->string('image', 100);
            $table->integer('view_order')->unsigned()->index();
            $table->integer('views')->unsigned()->index();
            $table->integer('rank')->unsigned()->index();
            $table->integer('created_by')->unsigned()->index();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lesson_details');
    }
}
