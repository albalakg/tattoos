<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id')->unsigned()->index();
            $table->text('description');
            $table->string('image', 100);
            $table->string('course_trailer', 100);
            $table->decimal('price')->unsigned();
            $table->integer('views')->unsigned();
            $table->integer('purchases')->unsigned();
            $table->integer('comments')->unsigned();
            $table->decimal('rank')->unsigned();
            $table->decimal('duration')->unsigned();
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
        Schema::dropIfExists('course_details');
    }
}
