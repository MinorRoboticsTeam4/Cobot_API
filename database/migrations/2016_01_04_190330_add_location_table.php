<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->double('coordinate_x');
            $table->double('coordinate_y');
            $table->double('coordinate_z');
            $table->double('orientation_x');
            $table->double('orientation_y');
            $table->double('orientation_z');
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
        Schema::drop('location');
    }
}
