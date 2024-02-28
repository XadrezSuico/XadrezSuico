<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventClassificateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_classificate_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references("id")->on("categoria");

            $table->integer('category_classificator_id')->unsigned();
            $table->foreign('category_classificator_id')->references("id")->on("categoria");

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
        Schema::dropIfExists('event_classificate_categories');
    }
}
