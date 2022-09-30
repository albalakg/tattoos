<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lesson_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_lesson_id')->index();
            $table->unsignedBigInteger('skill_id')->index();
            $table->unsignedBigInteger('type')->index()->comment('0 = requires, 1 = learns');
            $table->dateTime('created_at');
            $table->unsignedBigInteger('created_by')->index();
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
        Schema::dropIfExists('course_lesson_skills');
    }
}
