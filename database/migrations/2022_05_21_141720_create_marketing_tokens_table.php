<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120)->unique()->nullable();
            $table->string('phone', 15)->unique()->nullable();
            $table->string('token', 50)->unique();
            $table->integer('discount')->index()->unsigned()->comment('the discount is in shekels');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->index()->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketing_tokens');
    }
}
