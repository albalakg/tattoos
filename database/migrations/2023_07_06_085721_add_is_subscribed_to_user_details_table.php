<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSubscribedToUserDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->tinyInteger('is_subscribed')->after('user_id')->default(0)->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_details', 'is_subscribed')) {
            Schema::table('user_details', function (Blueprint $table) {
                $table->dropColumn('is_subscribed');
            });
        }
    }
}
