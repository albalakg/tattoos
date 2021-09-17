<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->integer('category_id')->unsigned()->index();
            $table->integer('status')->unsigned()->index();
            $table->string('image', 255)->nullable();
            $table->string('trailer', 255)->nullable();
            $table->decimal('price')->nullable();
            $table->text('description')->nullable();
            $table->integer('discount')->unsigned()->nullable();
            $table->integer('view_order')->unsigned();
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('courses');
    }
}
