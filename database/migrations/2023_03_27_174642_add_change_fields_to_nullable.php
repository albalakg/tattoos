<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeFieldsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('equipment', 'description')) {
            Schema::table('equipment', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
        }

        if (Schema::hasColumn('skills', 'description')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
