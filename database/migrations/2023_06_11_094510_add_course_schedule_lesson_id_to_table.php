<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseScheduleLessonIdToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_course_schedule_lessons', function($table) {
            $table->unsignedInteger('course_schedule_lesson_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_course_schedule_lessons', 'type_id')) {
            Schema::table('user_course_schedule_lessons', function (Blueprint $table) {
                $table->dropColumn('course_schedule_lesson_id');
            });
        }
    }
}
