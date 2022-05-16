<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id')->nullable()->unsigned()->index();
            $table->integer('course_area_id')->nullable()->unsigned()->index();
            $table->integer('video_id')->unsigned();
            $table->string('image', 255)->nullable();
            $table->integer('status')->unsigned()->index();
            $table->integer('view_order')->unsigned()->index();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->numeric('rehearsals')->nullable()->description('the amount of rehearsals');
            $table->numeric('rest_time')->nullable()->description('rest time in minutes');
            $table->numeric('activity_time')->nullable()->description('activity time in seconds');
            $table->numeric('activity_period')->nullable()->description('activity period in hours');
            $table->timestamps();
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
        Schema::dropIfExists('course_lessons');
    }
}
