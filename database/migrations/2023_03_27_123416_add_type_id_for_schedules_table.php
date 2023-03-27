<?php

use App\Domain\Content\Models\CourseScheduleLesson;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeIdForSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_schedule_lessons', function($table) {
            $table->unsignedInteger('type_id')->after('id')->default(CourseScheduleLesson::LESSON_TYPE_ID);
        });

        Schema::table('user_course_schedule_lessons', function($table) {
            $table->unsignedInteger('type_id')->after('id')->default(CourseScheduleLesson::LESSON_TYPE_ID);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('course_schedule_lessons', 'type_id')) {
            Schema::table('course_schedule_lessons', function (Blueprint $table) {
                $table->dropColumn('type_id');
            });
        }

        if (Schema::hasColumn('user_course_schedule_lessons', 'type_id')) {
            Schema::table('user_course_schedule_lessons', function (Blueprint $table) {
                $table->dropColumn('type_id');
            });
        }
    }
}
