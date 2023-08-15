<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Challenges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->integer('video_id')->index()->unsigned();
            $table->integer('status')->index()->unsigned();
            $table->string('name', 80)->unique();
            $table->text('description');
            $table->dateTime('expired_at');
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
        Schema::dropIfExists('challenges');
    }
}
