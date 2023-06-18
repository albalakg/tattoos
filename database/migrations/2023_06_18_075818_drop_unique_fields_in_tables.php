<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueFieldsInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_categories', function (Blueprint $table) {
            $table->dropUnique('name');
        });
        
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique('courses_name_unique');
        });
       
        Schema::table('videos', function (Blueprint $table) {
            $table->dropUnique('videos_name_unique');
        });
       
        Schema::table('support_categories', function (Blueprint $table) {
            $table->dropUnique('support_categories_name_unique');
        });
       
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropUnique('equipment_name_unique');
        });
       
        Schema::table('terms', function (Blueprint $table) {
            $table->dropUnique('terms_name_unique');
        });
       
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique('skills_name_unique');
        });
       
        Schema::table('training_options', function (Blueprint $table) {
            $table->dropUnique('training_options_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_categories', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('courses', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('videos', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('support_categories', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('equipment', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('terms', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('skills', function (Blueprint $table) {
            $table->unique('name');
        });
        
        Schema::table('training_options', function (Blueprint $table) {
            $table->unique('name');
        });
    }
}
