<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonTrainingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_training_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_lesson_id')->index();
            $table->unsignedBigInteger('training_option_id')->index();
            $table->unsignedInteger('value')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_training_options');
    }
}
