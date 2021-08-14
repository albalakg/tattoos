<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuEmailSentUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lu_email_sent_users', function (Blueprint $table) {
            $table->integer('email_sent_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('status')->unsigned()->index();
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
        Schema::dropIfExists('lu_email_sent_users');
    }
}
