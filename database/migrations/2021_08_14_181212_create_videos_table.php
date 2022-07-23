<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->unique();
            $table->text('description')->nullable();
            $table->integer('status')->unsigned()->index();
            $table->integer('video_length')->index()->unsigned();
            $table->string('video_path', 100);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
