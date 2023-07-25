<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengeTrainingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenge_training_options', function (Blueprint $table) {
            $table->integer('challenge_id')->index()->unsigned();
            $table->integer('training_option_id')->index()->unsigned();
            $table->integer('value')->index()->unsigned();
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
        Schema::dropIfExists('challenge_training_options');
    }
}
