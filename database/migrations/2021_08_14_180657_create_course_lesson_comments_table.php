<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lesson_comments', function (Blueprint $table) {
            $table->integer('course_lesson_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->text('message');
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
        Schema::dropIfExists('course_lesson_comments');
    }
}
