<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_areas', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id')->unsigned()->index();
            $table->integer('trainer_id')->unsigned()->index()->nullable();
            $table->string('name', 100);
            $table->text('description');
            $table->string('image', 100);
            $table->string('trailer', 100)->nullable();
            $table->integer('view_order')->unsigned()->index();
            $table->integer('status')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('course_areas');
    }
}
