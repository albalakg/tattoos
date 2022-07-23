<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoliciesUsersVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies_users_verifications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->unsigned();
            $table->integer('tnc_id')->index()->unsigned();
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
        Schema::dropIfExists('policies_users_verifications');
    }
}
