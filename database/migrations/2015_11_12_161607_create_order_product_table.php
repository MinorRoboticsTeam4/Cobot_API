<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('name');
            $table->integer('type');
            $table->string('image_path');
            $table->integer('option_strength');
            $table->integer('option_milk');
            $table->integer('option_sugar');
            $table->boolean('option_mug');
            $table->softDeletes();
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
        Schema::drop('order_products');
    }
}
