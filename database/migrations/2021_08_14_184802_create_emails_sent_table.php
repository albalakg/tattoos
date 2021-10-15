<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails_sent', function (Blueprint $table) {
            $table->id();
            $table->integer('email_type_id')->unsigned()->index();
            $table->text('parameters')->nullable();
            $table->integer('status')->unsigned()->index();
            $table->dateTime('created_at');
            $table->dateTime('finished_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails_sent');
    }
}
