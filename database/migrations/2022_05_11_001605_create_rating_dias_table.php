<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingDiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_dias', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('ratings_id')->unsigned();
            $table->foreign('ratings_id')->references("id")->on("ratings");

            $table->date('date');
            $table->integer('value');

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
        Schema::dropIfExists('rating_dias');
    }
}
