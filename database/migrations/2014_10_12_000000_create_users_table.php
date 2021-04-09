<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('role_id')->index()->unsigned();
            $table->string('first_name', 40);
            $table->string('last_name', 40);
            $table->string('email')->unique();
            $table->string('phone', 25)->nullable()->unique();
            $table->string('password');
            $table->integer('status')->index()->unsigned()->default(2);
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
