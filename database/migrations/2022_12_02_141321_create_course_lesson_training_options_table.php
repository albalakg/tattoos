<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonTrainingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lesson_training_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_lesson_id')->index();
            $table->unsignedBigInteger('training_option_id')->index();
            $table->unsignedInteger('value')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->index()->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lesson_training_options');
    }
}
