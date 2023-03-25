<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTeamScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_team_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_team_awards_id')->unsigned()->nullable();
            $table->foreign('event_team_awards_id')->references("id")->on("event_team_awards");
            $table->integer('clubs_id')->unsigned()->nullable();
            $table->foreign('clubs_id')->references("id")->on("clube");
            $table->integer('place');
            $table->decimal('score',8,1);
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
        Schema::dropIfExists('event_team_scores');
    }
}
