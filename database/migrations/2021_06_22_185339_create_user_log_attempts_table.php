<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_log_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 120);
            $table->string('user_agent', 200);
            $table->string('ip', 50);
            $table->integer('status')->index()->unsigned();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_log_attempts');
    }
}
