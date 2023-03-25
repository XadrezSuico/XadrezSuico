<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiebreakTeamAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiebreak_team_awards', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('event_team_awards_id')->unsigned();
            $table->foreign('event_team_awards_id')->references('id')->on("event_team_awards");
            $table->integer('tiebreaks_id')->unsigned();
            $table->foreign('tiebreaks_id')->references('id')->on("criterio_desempate");
            $table->integer('priority');
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
        Schema::dropIfExists('tiebreak_team_awards');
    }
}
