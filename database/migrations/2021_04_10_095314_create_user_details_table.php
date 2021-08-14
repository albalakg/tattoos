<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->primary('user_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 50)->unique()->nullable();
            $table->integer('gender')->unsigned()->nullable();
            $table->dateTime('birth_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
