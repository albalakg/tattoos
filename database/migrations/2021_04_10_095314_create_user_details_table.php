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
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 50)->unique()->nullable();
            $table->integer('gender')->unsigned()->nullable();
            $table->integer('team_id')->unsigned()->nullable()->index();
            $table->integer('city_id')->unsigned()->nullable()->index();
            $table->date('birth_date')->nullable();
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
