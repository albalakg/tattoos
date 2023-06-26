<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserCourseSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_course_schedules', function($table) {
            $table->renameColumn('user_id', 'user_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_course_schedules', 'user_course_id')) {
            Schema::table('user_course_schedules', function (Blueprint $table) {
                $table->renameColumn('user_course_id', 'user_id');
            });
        }
    }
}
