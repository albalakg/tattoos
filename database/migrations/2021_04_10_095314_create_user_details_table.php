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
            $table->integer('user_id');
            $table->primary('user_id');
            $table->integer('gender')->unsigned()->nullable();
            $table->dateTime('birth_date')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('street', 200)->nullable();
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
