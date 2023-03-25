<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTeamAwardCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_team_award_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_team_awards_id')->unsigned();
            $table->foreign('event_team_awards_id')->references('id')->on("event_team_awards");
            $table->integer('categories_id')->unsigned();
            $table->foreign('categories_id')->references('id')->on("categoria");
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
        Schema::dropIfExists('event_team_award_categories');
    }
}
