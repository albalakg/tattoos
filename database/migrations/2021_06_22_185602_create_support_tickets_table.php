<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('support_category_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->string('full_name', 40)->index()->nullable();
            $table->string('email', 120)->index()->nullable();
            $table->string('support_number', 10);
            $table->text('description');
            $table->integer('status')->unsigned()->index();
            $table->string('file_path', 100)->nullable();
            $table->timestamps();
            $table->dateTime('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
}
