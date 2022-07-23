<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_submissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_course_id')->unsigned()->index();
            $table->string('video', 100);
            $table->integer('status')->unsigned()->index();
            $table->text('comment')->nullable();
            $table->dateTime('finished_at')->nullable();
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
        Schema::dropIfExists('user_course_submissions');
    }
}
