<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiebreakTeamAwardValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiebreak_team_award_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_team_scores_id')->unsigned();
            $table->foreign('event_team_scores_id')->references('id')->on("event_team_scores");

            $table->integer('tiebreaks_id')->unsigned();
            $table->foreign('tiebreaks_id')->references('id')->on("criterio_desempate");

            $table->integer('priority');
            $table->decimal('value',8,2);
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
        Schema::dropIfExists('tiebreak_team_award_values');
    }
}
