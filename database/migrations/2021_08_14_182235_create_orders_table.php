<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('content_type_id')->unsigned()->index();
            $table->integer('content_id')->unsigned()->index();
            $table->integer('supplier_id')->unsigned()->index()->nullable();
            $table->integer('marketing_token_id')->unsigned()->index()->nullable();
            $table->string('order_number', 10);
            $table->string('token', 100)->nullable();
            $table->integer('status')->unsigned()->index();
            $table->decimal('price');
            $table->integer('coupon_id')->unsigned()->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_orders');
    }
}
