<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserResetPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reset_passwords', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->index();
            $table->string('token', 50);
            $table->dateTime('created_at');
            $table->dateTime('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_reset_passwords');
    }
}
