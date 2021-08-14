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
            $table->id();
            $table->integer('role_id')->index()->unsigned();
            $table->string('email')->unique()->index()->unique();
            $table->string('password')->index();
            $table->integer('status')->index()->unsigned();
            $table->integer('remember_me')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->index()->unsigned();
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
