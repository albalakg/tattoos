<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMarketingTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_tokens', function($table) {
            $table->renameColumn('discount', 'fee');
            $table->integer('course_id')->unsigned()->index()->after('id');
            $table->integer('status')->unsigned()->index()->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('marketing_tokens', 'fee')) {
            Schema::table('marketing_tokens', function (Blueprint $table) {
                $table->renameColumn('fee', 'discount');
            });
        }

        if (Schema::hasColumn('marketing_tokens', 'course_id')) {
            Schema::table('marketing_tokens', function (Blueprint $table) {
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasColumn('marketing_tokens', 'status')) {
            Schema::table('marketing_tokens', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
